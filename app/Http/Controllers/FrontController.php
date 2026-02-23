<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ExerciceTeam;
use Spatie\Permission\Models\Role;

class FrontController extends Controller
{
  public function index()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    return view('content.front.home', compact('organisation', 'social'));
  }

  public function about()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    return view('content.front.about', compact('organisation', 'social'));
  }

  public function projectsAndActivities()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    $activities = Activity::with('images')->get(['id', 'name', 'banner', 'place', 'description', 'string_id']);
    return view('content.front.projects-and-activities', compact('organisation', 'activities', 'social'));
  }

  public function cdl()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    $exercice = currentExercice();
    $exerciceId = $exercice ? $exercice->id : 0;

    $exerciceTeam = ExerciceTeam::where('exercice_id', $exerciceId)->orderBy('role_id', 'asc')->get();
    return view('content.front.cdl', compact('organisation', 'social', 'exercice', 'exerciceTeam'));
  }

  public function pastsPresident()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    $presidentId = Role::where('name', 'Président')->first()->id;
    $pastPresidents = ExerciceTeam::where('role_id', $presidentId)->orderBy('exercice_id', 'desc')->get();
    return view('content.front.pasts-president', compact('organisation', 'social', 'pastPresidents'));
  }

  public function contact()
  {
    $organisation = getCurrentOrganisation();
    $social = optional($organisation)->social;
    return view('content.front.contact', compact('organisation', 'social'));
  }
}
