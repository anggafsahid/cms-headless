<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;  // Assuming you will create a Team model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    // Display all team members
    public function index()
    {
        $teamMembers = Team::all();
        return view('admin.team.index', compact('teamMembers'));
    }

    // Show the form to create a new team member
    public function create()
    {
        return view('admin.team.create');
    }

    // Store a new team member
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:1024',  // Example validation for profile picture
        ]);

        $teamMember = new Team();
        $teamMember->name = $request->name;
        $teamMember->role = $request->role;
        $teamMember->bio = $request->bio;

        // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            $teamMember->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $teamMember->save();

        return redirect()->route('admin.team.index')->with('success', 'Team member created successfully');
    }

    // Show the form to edit an existing team member
    public function edit(Team $team)
    {
        return view('admin.team.edit', compact('team'));
    }

    // Update an existing team member
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:1024',
        ]);

        $team->name = $request->name;
        $team->role = $request->role;
        $team->bio = $request->bio;

        // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            $team->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $team->save();

        return redirect()->route('admin.team.index')->with('success', 'Team member updated successfully');
    }

    // Delete a team member
    public function destroy(Team $team)
    {
        // Delete Profile Picture the file from storage
        if ($team->profile_picture) {
            Storage::disk('public')->delete($team->profile_picture);
        }
        $team->delete();
        return redirect()->route('admin.team.index')->with('success', 'Team member deleted successfully');
    }
    
    
    // Public Index (for listing pages publicly)
    public function publicIndex()
    {
        $teamMembers = Team::all();
        return view('team.index', compact('teamMembers'));
    }

}
