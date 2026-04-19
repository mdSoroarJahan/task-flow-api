<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Base\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Illuminate\Support\Facades\Hash;

class authController extends BaseApiController
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return $this->success(new UserResource($user), 'User registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credential', 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successfully');
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $cacheKey = "user_profile_{$user->id}";

        // if (FacadesCache::has($cacheKey)) {
        //     logger("Profile loaded from cache: {$cacheKey}");
        // } else {
        //     logger("Profile cache miss, hitting DB: {$cacheKey}");
        // }
        $cached = FacadesCache::remember($cacheKey, now()->addHour(), function () use ($user) {
            return new UserResource($user);
        });
        return $this->success($cached, 'User profile retrieved successfully');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $cacheKey = "user_profile_($user->id)";
        FacadesCache::forget($cacheKey);
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logout Successfully');
    }
}
