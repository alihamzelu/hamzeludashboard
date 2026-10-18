<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProjectMemberController extends Controller
{
    public function createUser(Request $request, Project $project)
    {
        $this->authorize('inviteMember', $project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:member,manager'],
        ]);

        $user = DB::transaction(function () use ($validated, $project) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $project->members()->attach($user->id, [
                'role' => $validated['role'],
            ]);

            return $user;
        });


        return back()->with(
            'success',
            'کاربر با موفقیت ایجاد و به پروژه اضافه شد.'
        );
    }
}