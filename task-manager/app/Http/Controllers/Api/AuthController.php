<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Enums\UserStatus;
use Illuminate\Validation\Rule;



class AuthController extends Controller
{
    public function login(Request $request)
{   

    
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (!auth()->attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = auth()->user();

    

if ($user->status !== UserStatus::Approved->value) {
    auth()->logout();

    return response()->json([
        'message' => 'Account in attesa di approvazione'
    ], 403);
}

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user
    ]);
}
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }



public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'status' => UserStatus::Pending->value,
        'role_id' => null,
    ]);

    return response()->json([
        'message' => 'Registrazione completata. Account in attesa di approvazione.'
    ], 201);
}
}