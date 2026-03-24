<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Enums\UserStatus;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    private function ensureAdmin($user)
    {
        if ($user->role?->name !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request->user());

        $users = User::with('role')
            ->select('id', 'name', 'email', 'status', 'role_id', 'created_at')
            
            ->get();

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

        return response()->json(['message' => 'User approved']);
    }

    public function reject(Request $request, User $user)
    {
        $this->ensureAdmin($request->user());

        $user->update([
            'status' => UserStatus::Rejected->value,
            'role_id' => null,
        ]);

        return response()->json(['message' => 'User rejected']);
    }

    public function updateRole(Request $request, User $user)
    {
        $this->ensureAdmin($request->user());

        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update([
            'role_id' => $request->role_id,
        ]);

        return response()->json(['message' => 'Role updated']);
    }

    public function destroy(Request $request, User $user)
{
    // Verifica che sia un admin
    if ($request->user()->role_id !== 1) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }

    // Impedisce all'admin di eliminare sé stesso
    if ($request->user()->id === $user->id) {
        return response()->json([
            'message' => 'Non puoi eliminare il tuo account'
        ], 400);
    }

    $user->delete();

    return response()->json([
        'message' => 'Utente eliminato con successo'
    ]);
}
}