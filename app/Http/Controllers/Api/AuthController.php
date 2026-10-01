<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService) { $this->userService = $userService; }

    public function login(ApiLoginRequest $request) {
        $result = $this->userService->authenticateApi($request->email, $request->password, $request->device_name);
        if (!$result) return response()->json(['message' => 'Credenciales inválidas'], 401);
        return response()->json(['token' => $result['token']]);
    }

    public function user(Request $request) {
        $user = $request->user();
        $user->load('roles');
        return $user;
    }

    public function logout(Request $request) {
        $this->userService->logoutApi($request->user());
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
