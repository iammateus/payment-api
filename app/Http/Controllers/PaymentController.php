<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\SessionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /* 
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    } 
    */

    public function store (Request $request): JsonResponse
    {
        $this->validate($request, [
            'method' => 'required|in:BOLETO',
            'sender' => 'required',
            'sender.name' => 'required|string|min_words:2',
        ]);
        return response()->json();
    } 
}
