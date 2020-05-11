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
            'method' => 'required',
            'sender.name' => 'required',
            'sender.document.value' => 'required',
            'sender.phone.number' => 'required',
            'sender.email' => 'required',
            'sender.hash' => 'required',
            'items' => 'required',
            // 'items.*.id' => 'required',
            // 'items.*.description' => 'required',
            // 'items.*.quantity' => 'required',
            // 'items.*.amount' => 'required'
        ]);

        return response()->json();
    } 
}
