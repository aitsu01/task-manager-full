<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    private function ensureAdmin($user)
    {
        if (!$user->role || $user->role->name !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function pending(Request $request)
    {
        $this->ensureAdmin($request->user());

        $users = User::where('status', UserStatus::Pending->value)
            ->get(['id', 'name', 'email', 'created_at']);

        return response()->json($users);
    }

    public function approve(Request $request, User $user)
    {
        $this->ensureAdmin($request->user());

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update([
            'status' => UserStatus::Approved->value,
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'message' => 'User approved successfully'
        ]);
    }
}