<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teams = $request->user()->teams()->withCount('members')->get();

        return response()->json([
            'status' => 'success',
            'data' => $teams,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $team = $request->user()->ownedTeams()->create($validated);

        $team->members()->create([
            'user_id' => $request->user()->id,
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        $team->loadCount('members');

        return response()->json([
            'status' => 'success',
            'data' => $team,
        ], 201);
    }

    public function show(Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        $team->load(['owner', 'members.user', 'tasks']);

        return response()->json([
            'status' => 'success',
            'data' => $team,
        ]);
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
        ]);

        $team->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $team->fresh(),
        ]);
    }

    public function destroy(Team $team): JsonResponse
    {
        Gate::authorize('delete', $team);

        $team->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Team deleted',
        ]);
    }

    public function members(Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        $members = $team->members()->with('user')->get();

        return response()->json([
            'status' => 'success',
            'data' => $members,
        ]);
    }

    public function addMember(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role' => ['sometimes', 'string', 'in:admin,member'],
        ]);

        if ($team->hasMember(User::find($validated['user_id']))) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is already a member of this team',
            ], 409);
        }

        $member = $team->members()->create([
            'user_id' => $validated['user_id'],
            'role' => $validated['role'] ?? 'member',
            'joined_at' => now(),
        ]);

        $member->load('user');

        return response()->json([
            'status' => 'success',
            'data' => $member,
        ], 201);
    }

    public function joinByInvite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invite_code' => ['required', 'string', 'max:20'],
        ]);

        $team = Team::where('invite_code', $validated['invite_code'])->first();

        if (!$team) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid invite code',
            ], 404);
        }

        if ($team->hasMember($request->user())) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are already a member of this team',
            ], 409);
        }

        $member = $team->members()->create([
            'user_id' => $request->user()->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $member->load('user');

        return response()->json([
            'status' => 'success',
            'data' => $member,
        ], 201);
    }

    public function removeMember(Team $team, TeamMember $member): JsonResponse
    {
        Gate::authorize('delete', $team);

        if ($member->team_id !== $team->id) {
            abort(404);
        }

        if ($member->role === 'admin' && $team->members()->where('role', 'admin')->count() <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot remove the last admin',
            ], 409);
        }

        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Member removed',
        ]);
    }
}
