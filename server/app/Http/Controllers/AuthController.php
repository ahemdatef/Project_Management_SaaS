<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $organization = null;

            // 2. Create Organization (ONLY IF PROVIDED)
            if ($request->filled('organization_name')) {
                $slug = Str::slug($request->organization_name);

                $count = Organization::where('slug', 'LIKE', "{$slug}%")->count();
                if ($count > 0) {
                    $slug .= '-' . ($count + 1);
                }

                $organization = Organization::create([
                    'name' => $request->organization_name,
                    'slug' => $slug,
                    'owner_id' => $user->id,
                ]);

                // Attach and Set Context
                $user->organizations()->attach($organization->id, ['role' => 'admin']);
                $user->update(['current_organization_id' => $organization->id]);
            }

            // 3. Create Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                // Return slug if org was created, otherwise null
                'organization_slug' => $organization ? $organization->slug : null
            ], 201);
        });
    }



    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create a new personal access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get the slug of the user's current organization
        $organizationSlug = $user->currentOrganization ? $user->currentOrganization->slug : null;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'organization_slug' => $organizationSlug,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
