<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\FcmTokenRequest;
use App\Services\UserService;

class FcmController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService) { $this->userService = $userService; }

    public function register(FcmTokenRequest $request) {
        $this->userService->updateFcmToken($request->user(), $request->token);
        return response()->json(['message' => 'FCM token registrado correctamente.']);
    }
}
