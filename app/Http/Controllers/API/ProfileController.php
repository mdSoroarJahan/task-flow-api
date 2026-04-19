<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Base\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cache;

class ProfileController extends BaseApiController
{
    public function update(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = FacadesAuth::user();
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);
        $cacheKey = "user_profile_{$user->id}";
        Cache::put($cacheKey, new UserResource($user), now()->addHour());
        return $this->success(new UserResource($user), 'Profile updated successfully');
    }
}
