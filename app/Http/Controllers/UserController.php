<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    public function indexDashboard(Request $request)
    {
       
        $users = User::all();

        return Inertia::render('Dashboard/Users', [
            'users' => $users,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => 'required|string|in:admin,editor,user', 
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return Redirect::route('users.dashboard')->with('success', 'User created successfully.');
    }

    
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|string|in:admin,editor,user',
        ]);

        $user->update($validated);

        return Redirect::route('users.dashboard')->with('success', 'User updated successfully.');
    }

   
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.dashboard');
    }
}
