<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectMember;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $Projects = Project::all();
        $Tasks = Task::all();
        $TeamMembers = ProjectMember::all();

        return view('home', compact('Projects', 'Tasks', 'TeamMembers'));
    }

}
