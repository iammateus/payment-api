<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\SessionService;

class SessionController extends Controller
{
    private SessionService $sessionService;

    public function __construct ( SessionService $sessionService )
    {
        $this->sessionService = $sessionService;
    }

    public function getSessionTokenFromPagseguroApi (): JsonResponse
    {
        $session = $this->sessionService->getSessionTokenFromPagseguroApi();

        $token = $session['id'];

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'token' => $token
            ]
        ]);
    } 
}
