<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendaRequest;
use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AgendaController extends Controller
{
  public function index()
  {
    $this->authorize('agendas.index');
    return view('content.agenda.index');
  }

  public function events(Request $request)
  {
    $this->authorize('agendas.index');

    $query = Agenda::query();

    if ($request->has('types')) {
      $types = is_array($request->types) ? $request->types : explode(',', $request->types);
      $query->whereIn('type', $types);
    }

    $events = $query->get()->map(function ($agenda) {
      return [
        'id' => $agenda->string_id,
        'title' => $agenda->title,
        'start' => $agenda->begin_at,
        'end' => $agenda->end_at,
        'extendedProps' => [
          'type' => $agenda->type,
          'description' => $agenda->description ?? '',
          'location' => $agenda->location ?? '',
          'banner' => $agenda->banner ?? '',
        ],
        'classNames' => ['event-' . $agenda->type]
      ];
    });

    return response()->json($events);
  }

  public function store(AgendaRequest $request)
  {
    $this->authorize('agendas.create');

    $validatedData = $request->validated();

    DB::beginTransaction();
    try {
      $validatedData['exercice_id'] = currentExercice()->id;
      $validatedData['active'] = true;

      if ($request->hasFile('banner')) {
        $validatedData['banner'] = $request->file('banner')->store('agendas/banners', 'public');
      }

      $agenda = Agenda::create($validatedData);

      DB::commit();
      return response()->json([
        'status' => 'success',
        'message' => __('Event created successfully'),
        'data' => $agenda
      ], 200);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => __('Event creation failed'),
        'errors' => $e->getMessage()
      ], 500);
    }
  }

  public function update(AgendaRequest $request, Agenda $agenda)
  {
    $this->authorize('agendas.edit');

    $validatedData = $request->validated();

    try {
      if ($request->hasFile('banner')) {
        if ($agenda->banner) {
          Storage::disk('public')->delete($agenda->banner);
        }
        $validatedData['banner'] = $request->file('banner')->store('agendas/banners', 'public');
      }

      $agenda->update($validatedData);

      return response()->json([
        'status' => 'success',
        'message' => __('Event updated successfully'),
        'data' => $agenda
      ], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'status' => 'error',
        'message' => __('Event update failed'),
        'errors' => $th->getMessage()
      ], 500);
    }
  }

  public function destroy(Agenda $agenda)
  {
    $this->authorize('agendas.destroy');

    $agenda->delete();

    return response()->json([
      'status' => 'success',
      'message' => __('Event deleted successfully')
    ]);
  }
}
