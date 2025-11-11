<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trail\TrailStoreRequest;
use App\Http\Requests\Trail\TrailUpdateRequest;
use App\Models\Trail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(DB::table('trails')->paginate(50));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrailStoreRequest $request)
    {
        $this->middleware('auth:sanctum');

        // Faut valider d'abord
        $validate = $request->validated();

        // Magie, ca marche
        $trail = new Trail($validate);

        return $request->user()->trails()->save($trail);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        return response()->json(DB::table('trails')->where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrailUpdateRequest $request, string $id)
    {
        $this->middleware('auth:sanctum');

        $trail = Trail::findOrFail($id);

        if ($trail && $request->user()->can('update', $trail)) {
            $trail->update($request->validated());

            return response()->json(['message' => 'Trail updated successfully', 'trail' => $trail]);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $this->middleware('auth:sanctum');

        $trail = Trail::findOrFail($id);

        if ($request->user()->can('delete', $trail)) {
            Trail::destroy($id);

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
