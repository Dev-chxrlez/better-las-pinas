<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::create(['name'=>$request->string('full_name')->toString(),'email'=>strtolower($request->string('email')->toString()),'password'=>$request->string('password')->toString()]);
            $user->assignRole('resident');
            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();
        $this->audit($request, $user, 'auth.register');
        return response()->json(['success'=>true,'message'=>'Account created.','data'=>['user'=>$this->userPayload($user)]], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt(['email'=>strtolower($request->string('email')->toString()),'password'=>$request->string('password')->toString()])) {
            return response()->json(['success'=>false,'message'=>'The provided credentials are incorrect.','errors'=>['email'=>['The provided credentials are incorrect.']]], 422);
        }
        $request->session()->regenerate();
        $user = $request->user();
        $this->audit($request, $user, 'auth.login');
        return response()->json(['success'=>true,'message'=>'Signed in.','data'=>['user'=>$this->userPayload($user)]]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->audit($request, $user, 'auth.logout');
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::forgetGuards();
        return response()->json(['success'=>true,'message'=>'Signed out.','data'=>null]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['success'=>true,'message'=>'Authenticated user.','data'=>['user'=>$this->userPayload($request->user())]]);
    }

    private function userPayload(User $user): array
    {
        return ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->getRoleNames()->first()];
    }

    private function audit(Request $request, User $user, string $action): void
    {
        AuditLog::create(['actor_id'=>$user->id,'action'=>$action,'subject_type'=>User::class,'subject_id'=>(string)$user->id,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
    }
}
