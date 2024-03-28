<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Error;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|JsonResource
    {
        if (auth()->user()->role !== RoleEnum::ADMIN->value) {
            $error = new Error("Unauthorized", HttpResponse::HTTP_UNAUTHORIZED);

            return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
