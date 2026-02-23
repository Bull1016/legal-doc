<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExerciceStoreRequest;
use App\Http\Requests\ExerciceUpdateRequest;
use App\Models\Exercice;
use App\Models\ExerciceTeam;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class ExerciceController extends Controller
{
  public function index(Request $request)
  {
    $this->authorize('mandates.index');
    return view('content.exercices.index');
  }

  public function getData(Request $request)
  {
    $this->authorize('mandates.index');
    $query = Exercice::select(['id', 'string_id', 'name', 'logo', 'slogan', 'year', 'created_at']);

    return DataTables::of($query)
      ->addColumn('logo_name', function ($row) {
        $imagePath = $row->logo && file_exists(storage_path('app/public/' . $row->logo))
          ? asset('storage/' . $row->logo)
          : asset('assets/img/branding/default-mandate.webp'); // image par défaut

        return '
              <div class="justify-content-center">
                  <img alt="' . e($row->name) . '" src="' . $imagePath . '" width="50" class="mb-1">
                  <br>
                  <small class="text-muted">' . e($row->name) . '</small>
              </div>
          ';
      })
      ->addColumn('created_at', function ($row) {
        return formatDate($row->created_at);
      })
      ->addColumn('actions', function ($row) {
        $user = auth()->user();
        $editUrl = route('mandates.update', $row->string_id);
        $deleteUrl = route('mandates.destroy', $row->string_id);
        $manageTeamUrl = route('mandates.team.get', $row->string_id);

        $buttons = '<div class="d-inline-block text-nowrap">';

        if ($user->can('mandates.create')) {
          $buttons .= '
          <button type="button" class="btn btn-text-secondary rounded-pill waves-effect btn-icon manage-btn"
                        data-url="' . $manageTeamUrl . '"
                        data-id="' . $row->string_id . '"
                        data-name="' . $row->name . '"
                        title="' . __('Mandate Team') . '">
                    <i class="icon-base ti tabler-sitemap icon-25px"></i>
                </button>';
        }

        $buttons .= '
            <button type="button" class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base ti tabler-dots-vertical icon-25px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end m-0">';

        if ($user->can('mandates.edit')) {
          $buttons .= '
                <button type="button" class="dropdown-item edit-btn"
                        data-url="' . $editUrl . '"
                        data-id="' . $row->string_id . '"
                        data-slogan="' . $row->slogan . '"
                        data-year="' . $row->year . '"
                        data-name="' . $row->name . '">
                    <i class="icon-base ti tabler-pencil me-1"></i>' . __('Edit Mandate') . '
                </button>';
        }

        if ($user->can('mandates.destroy')) {
          $buttons .= '
                <button type="button" class="dropdown-item btn-delete"
                        data-url="' . $deleteUrl . '"
                        data-id="' . $row->string_id . '">
                    <i class="icon-base ti tabler-trash me-1"></i>' . __('Delete Mandate') . '
                </button>';
        }

        $buttons .= '
            </div>
        </div>
    ';
        return $buttons;
      })

      ->addIndexColumn()
      ->rawColumns(['actions', 'logo_name'])
      ->make(true);
  }

  public function store(ExerciceStoreRequest $request)
  {
    $this->authorize('mandates.create');
    try {
      $validatedData = $request->validated();

      if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('exercices', 'public');
        $validatedData['logo'] = $path;
      }

      Exercice::create($validatedData);

      return response()->json([
        'status' => 'success',
        'message' => __('Mandate created successfully'),
        'errors' => []
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Mandate creation failed'),
        'errors' => $e->errors(),
      ], 422);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Mandate creation failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function update(ExerciceUpdateRequest $request, $exercice)
  {
    $this->authorize('mandates.edit');
    try {
      $exercice = Exercice::where('string_id', $exercice)->first();
      $validatedData = $request->validated();

      if ($request->hasFile('logo')) {
        // Supprimer l'ancienne image si elle existe
        if ($exercice->logo && Storage::exists('public/' . $exercice->logo)) {
          Storage::delete('public/' . $exercice->logo);
        }

        $path = $request->file('logo')->store('exercices', 'public');
        $validatedData['logo'] = $path;
      }

      $exercice->update($validatedData);

      return response()->json([
        'status' => 'success',
        'message' => __('Mandate updated successfully'),
        'errors' => []
      ], 200);
    } catch (ValidationException $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Mandate update failed'),
        'errors' => $e->errors(),
      ], 422);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Mandate update failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function destroy($exercice)
  {
    $this->authorize('mandates.destroy');
    try {
      $exercice = Exercice::where('string_id', $exercice)->first();

      if ($exercice->logo && Storage::exists('public/' . $exercice->logo)) {
        Storage::delete('public/' . $exercice->logo);
      }
      $exercice->delete();

      return response()->json([
        'status' => 'success',
        'message' => __('Mandate deleted successfully')
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Mandate deletion failed'),
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function getTeam(Request $request, $mandate)
  {
    $this->authorize('mandates.create');

    $mandate = Exercice::where('string_id', $mandate)->first();

    $query = $mandate->team()->select(['id', 'string_id', 'exercice_id', 'member_id', 'role_id', 'created_at']);

    return DataTables::of($query)
      ->addColumn('created_at', function ($row) {
        return formatDate($row->created_at);
      })
      ->addColumn('position', function ($row) {
        return $row->role->name;
      })
      ->addColumn('member', function ($row) {
        return $row->member->full_name;
      })
      ->addColumn('actions', function ($row) {
        $user = auth()->user();
        $editUrl = route('mandates.team.update', ['mandate' => $row->exercice_id, 'team' => $row->string_id]);
        $deleteUrl = route('mandates.team.destroy', ['mandate' => $row->exercice_id, 'team' => $row->string_id]);

        $buttons = '
        <div class="d-inline-block text-nowrap">
            <button type="button" class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="icon-base ti tabler-dots-vertical icon-25px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end m-0">';

        if ($user->can('mandates.edit')) {
          $buttons .= '
                <button type="button" class="dropdown-item edit-region-btn"
                        data-url="' . $editUrl . '"
                        data-id="' . $row->string_id . '"
                        data-role="' . $row->role_id . '"
                        data-exercice="' . $row->exercice_id . '"
                        data-member="' . $row->member_id . '">
                    <i class="icon-base ti tabler-pencil me-1"></i>' . __('Edit') . '
                </button>';
        }

        if ($user->can('mandates.destroy')) {
          $buttons .= '
                <button type="button" class="dropdown-item btn-region-delete"
                        data-url="' . $deleteUrl . '"
                        data-role="' . $row->role_id . '"
                        data-exercice="' . $row->exercice_id . '"
                        data-member="' . $row->member_id . '"
                        data-id="' . $row->string_id . '">
                    <i class="icon-base ti tabler-trash me-1"></i>' . __('Delete') . '
                </button>';
        }

        $buttons .= '
            </div>
        </div>
    ';
        return $buttons;
      })

      ->addIndexColumn()
      ->rawColumns(['actions', 'created_at'])
      ->make(true);
  }

  public function getAvailableRoles(Request $request, $mandate)
  {
    $this->authorize('mandates.create');

    $mandate = Exercice::where('string_id', $mandate)->first();
    $allRoles = Role::where('name', '!=', 'superadmin')->where('name', '!=', 'member')->select(['id', 'name'])->get();

    $assignedRoleIds = $mandate->team()->pluck('role_id')->toArray();

    $roles = $allRoles->map(function ($role) use ($assignedRoleIds) {
      return [
        'id' => $role->id,
        'name' => $role->name,
        'disabled' => in_array($role->id, $assignedRoleIds)
      ];
    });

    return response()->json(['data' => $roles]);
  }

  public function getAvailableMembers(Request $request, $mandate)
  {
    $this->authorize('mandates.create');

    $mandate = Exercice::where('string_id', $mandate)->first();
    $allMembers = Member::with('user')
      ->where('organisation_id', getCurrentOrganisation()->id)
      ->get();
    $assignedMemberIds = $mandate->team()->pluck('member_id')->toArray();

    $members = $allMembers->filter(function ($member) {
      return $member->user !== null;
    })->map(function ($member) use ($assignedMemberIds) {
      return [
        'id' => $member->id,
        'name' => $member->full_name,
        'disabled' => in_array($member->id, $assignedMemberIds)
      ];
    })->values();

    return response()->json(['data' => $members]);
  }

  public function storeTeam(Request $request, $mandate)
  {
    $this->authorize('mandates.create');

    try {
      $mandate = Exercice::where('string_id', $mandate)->first();

      $validated = $request->validate([
        'role_id' => 'required|exists:roles,id',
        'member_id' => 'required|exists:members,id',
      ]);

      // Check if role is already assigned for this mandate
      $existingRole = $mandate->team()->where('role_id', $validated['role_id'])->first();
      if ($existingRole) {
        return response()->json([
          'status' => 'error',
          'message' => __('This position is already assigned for this mandate'),
        ], 422);
      }

      // Check if member is already assigned for this mandate
      $existingMember = $mandate->team()->where('member_id', $validated['member_id'])->first();
      if ($existingMember) {
        return response()->json([
          'status' => 'error',
          'message' => __('This member is already assigned to a position for this mandate'),
        ], 422);
      }

      $validated['exercice_id'] = $mandate->id;

      ExerciceTeam::create($validated);

      return response()->json([
        'status' => 'success',
        'message' => __('Team member added successfully'),
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Team member added failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function updateTeam(Request $request, $mandate, $team)
  {
    $this->authorize('mandates.edit');

    try {
      $mandate = Exercice::where('string_id', $mandate)->first();
      $team = ExerciceTeam::where('string_id', $team)->first();

      $validated = $request->validate([
        'role_id' => 'required|exists:roles,id',
        'member_id' => 'required|exists:members,id',
      ]);

      // Check if role is already assigned for this mandate (excluding current team member)
      $existingRole = $mandate->team()
        ->where('role_id', $validated['role_id'])
        ->where('id', '!=', $team->id)
        ->first();
      if ($existingRole) {
        return response()->json([
          'status' => 'error',
          'message' => __('This position is already assigned for this mandate'),
        ], 422);
      }

      // Check if member is already assigned for this mandate (excluding current team member)
      $existingMember = $mandate->team()
        ->where('member_id', $validated['member_id'])
        ->where('id', '!=', $team->id)
        ->first();
      if ($existingMember) {
        return response()->json([
          'status' => 'error',
          'message' => __('This member is already assigned to a position for this mandate'),
        ], 422);
      }

      $team->update($validated);

      return response()->json([
        'status' => 'success',
        'message' => __('Team member updated successfully'),
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Team member update failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function destroyTeam($mandate, $team)
  {
    $this->authorize('mandates.destroy');

    try {
      $team = ExerciceTeam::where('string_id', $team)->first();
      $team->delete();

      return response()->json([
        'status' => 'success',
        'message' => __('Team member deleted successfully')
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => __('Team member deletion failed'),
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
