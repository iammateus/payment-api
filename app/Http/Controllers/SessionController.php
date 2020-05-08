<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\SessionService;

class SessionController extends Controller
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function store (): JsonResponse
    {
        $session = $this->sessionService->store();

        $token = (string) $session->token;

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'token' => $token
            ]
        ]);
    } 
}
