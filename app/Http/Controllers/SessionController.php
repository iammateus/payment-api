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

    /**
     * Makes the session request to Pagseguro and returns the session token
     *
     * @return JsonResponse
     */
    public function store (): JsonResponse
    {
        $session = $this->sessionService->store();

        $token = $session['id'];

        return response()->json([
            'message' => 'SUCCESS',
            'data' => [
                'token' => $token
            ]
        ]);
    } 
}
