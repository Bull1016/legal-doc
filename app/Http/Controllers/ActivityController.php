<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class ActivityController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('activities.index');
    return view('content.activities.index');
  }

  public function getData(Request $request)
  {
    $this->authorize('activities.index');
    $query = Activity::select(['id', 'string_id', 'name', 'place', 'description', 'created_at']);

    return DataTables::of($query)
      ->addColumn('description', function ($row) {
        return $row->description;
      })
      ->addColumn('created_at', function ($row) {
        return formatDate($row->created_at);
      })
      ->addColumn('actions', function ($row) {
        $user = auth()->user();
        $editUrl = route('activities.edit', $row->string_id);
        $deleteUrl = route('activities.destroy', $row->string_id);

        $buttons = '
        <div class="dropdown text-center">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base ti tabler-dots-vertical icon-25px"></i>
            </button>
            <div class="dropdown-menu">';

        if ($user->can('activities.edit')) {
          $buttons .= '
                <a class="dropdown-item" href="' . $editUrl . '">
                    <i class="icon-base ti tabler-pencil me-1"></i> ' . __('Edit') . '
                </a>';
        }

        if ($user->can('activities.destroy')) {
          $buttons .= '
                <button type="button" class="dropdown-item btn-delete"
                        data-url="' . $deleteUrl . '"
                        data-id="' . $row->string_id . '">
                    <i class="icon-base ti tabler-trash me-1"></i> ' . __('Delete') . '
                </button>';
        }

        $buttons .= '
            </div>
        </div>
    ';
        return $buttons;
      })

      ->addIndexColumn()
      ->rawColumns(['description', 'actions', 'created_at'])
      ->make(true);
  }

  public function create()
  {
    $this->authorize('activities.create');

    return view('content.activities.create');
  }

  public function store(ActivityRequest $request)
  {
    $this->authorize('activities.create');

    if (!$request->hasFile('banner')) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity creation failed'),
        'errors' => ['banner' => __('Please upload an image')],
      ], 422);
    }

    try {
      $validatedData = $request->validated();
      $validatedData['organisation_id'] = getCurrentOrganisation()->id;
      $validatedData['exercice_id'] = currentExercice()->id;

      $path = $request->file('banner')->store('activities', 'public');
      $validatedData['banner'] = $path;

      $activity = Activity::create($validatedData);

      // Handle image annexes
      if ($request->hasFile('annexes')) {
        foreach ($request->file('annexes') as $image) {
          $imagePath = $image->store('activity_images', 'public');
          $activity->images()->create([
            'image_path' => $imagePath,
          ]);
        }
      }

      return response()->json([
        'status' => 'success',
        'message' => __('Activity created successfully'),
        'errors' => []
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity creation failed'),
        'errors' => $e->errors(),
      ], 422);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity creation failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function edit(Activity $activity)
  {
    $this->authorize('activities.edit');
    return view('content.activities.edit', compact('activity'));
  }

  public function update(ActivityRequest $request, Activity $activity)
  {
    $this->authorize('activities.edit');
    try {
      $validatedData = $request->validated();

      if ($request->hasFile('banner')) {
        // Supprimer l'ancienne image si elle existe
        if ($activity->banner && Storage::exists('public/' . $activity->banner)) {
          Storage::delete('public/' . $activity->banner);
        }

        $path = $request->file('banner')->store('activities', 'public');
        $validatedData['banner'] = $path;
      }

      $activity->update($validatedData);

      // Handle image annexes deletion
      if ($request->has('deleted_images')) {
        $deletedImages = json_decode($request->input('deleted_images'), true);
        if (is_array($deletedImages)) {
          foreach ($deletedImages as $imageId) {
            $image = $activity->images()->where('string_id', $imageId)->first();
            if ($image) {
              // Delete file from storage
              if (Storage::exists('public/' . $image->image_path)) {
                Storage::delete('public/' . $image->image_path);
              }
              // Delete database record
              $image->delete();
            }
          }
        }
      }

      // Handle new image annexes
      if ($request->hasFile('annexes')) {
        foreach ($request->file('annexes') as $image) {
          $imagePath = $image->store('activity_images', 'public');
          $activity->images()->create([
            'image_path' => $imagePath,
          ]);
        }
      }

      return response()->json([
        'status' => 'success',
        'message' => __('Activity updated successfully'),
        'errors' => []
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity update failed'),
        'errors' => $e->errors(),
      ], 422);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity update failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function destroy(Activity $activity)
  {
    $this->authorize('activities.destroy');
    try {
      $activity->images()->delete();
      $activity->delete();

      return response()->json([
        'status' => 'success',
        'message' => __('Activity deleted successfully')
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Activity deletion failed'),
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
