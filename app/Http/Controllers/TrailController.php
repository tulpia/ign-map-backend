<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trail\TrailStoreRequest;
use App\Http\Requests\Trail\TrailUpdateRequest;
use App\Http\Resources\TrailResource;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return TrailResource::collection(Trail::with('images')::paginate(50));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrailStoreRequest $request)
    {
        // On sauvegarde le trace
        $trail = new Trail($request->validated());

        return $request->user()->trails()->save($trail);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): TrailResource
    {
        return new TrailResource(Trail::with('images')->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrailUpdateRequest $request, string $id)
    {
        $trail = Trail::with('images')->findOrFail($id);

        if ($trail && $request->user()->can('update', $trail)) {
            $trail->update($request->validated());

            return response()->json(['message' => 'Trail updated successfully', 'trail' => $trail->toResource()]);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $trail = Trail::findOrFail($id);

        if ($request->user()->can('delete', $trail)) {
            Trail::destroy($id);

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
