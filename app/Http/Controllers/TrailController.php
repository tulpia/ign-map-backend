<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrailController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['test' => true]);
    }

    public function store(Request $request): Response {
        if (!auth()->check()) {
            return response('no', 401);
        }
    }
}
