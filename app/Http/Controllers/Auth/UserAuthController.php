<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAuthController extends Controller
{
    public function register(UserRegisterRequest $userRegisterRequest): JsonResponse
    {
        $userRequestValidated = $userRegisterRequest->validated();

        $userRequestValidated["password"] = bcrypt($userRequestValidated["password"]);

        $userRequestValidated['role'] = RoleEnum::USER->value;

        $user = User::create($userRequestValidated);

        $token_data = $user->createToken(env("SECRET_TOKEN_APP"));

        $access_token = $token_data->accessToken;

        $expires_in = Carbon::now()->diffInSeconds($token_data->token->expires_at);

        return self::authTokenResponse($user, $access_token, $expires_in);
    }

    public function login(UserLoginRequest $userLoginRequest): JsonResponse
    {
        $userLoginRequest->authenticate();

        $token_data = auth()->user()->createToken(env("SECRET_TOKEN_APP"));

        $access_token = $token_data->accessToken;

        $expires_in = Carbon::now()->diffInSeconds($token_data->token->expires_at);

        return self::authTokenResponse(auth()->user(), $access_token, $expires_in);
    }

    public function me()
    {
        return new UserResource(auth()->user());
    }

    public function logout(): JsonResponse
    {
        $token = auth()->user()->token();

        $token->revoke();

        $token->delete();

        return response()->json(['message' => 'Logged out successfully'], Response::HTTP_OK);
    }

    private static function authTokenResponse(User $user, string $access_token, int $expires_in): JsonResponse
    {   
        // TODO PASS THIS TO JSONRESOURCE(TRY TO PASS TOKEN VALUES LIKE A FIELD OF CLASS) FIND OUT
        $user = $user->only(['name', 'email', 'role', 'created_at', 'updated_at']);

        return response()->json(["user" => $user, "token" => [
            "access_token" => $access_token,
            "expires_in" => $expires_in,
            'token_type' => 'Bearer',
        ]], Response::HTTP_OK);
    }
}
