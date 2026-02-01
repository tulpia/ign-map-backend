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
    public function index(Request $request): AnonymousResourceCollection
    {
        $ressource = Trail::with('images');

        // Filtrage par bounding box (lat/lng)
        if ($request->filled('lat_min') && $request->filled('lat_max') && 
            $request->filled('lng_min') && $request->filled('lng_max')) {
            $ressource->whereBetween('latitude', [$request->input('lat_min'), $request->input('lat_max')])
                      ->whereBetween('longitude', [$request->input('lng_min'), $request->input('lng_max')]);
        }

        // Filtrage des valeurs dans la requete pour pas filtrer sur n'importe quoi
        $fillables = (new Trail())->getFillable();
        foreach ($fillables as $fillable) {
            if ($request->filled($fillable) && !in_array($fillable, ['latitude', 'longitude'])) {
                $ressource->where($fillable, $request->input($fillable));
            }
        }

        return TrailResource::collection($ressource->paginate(12));
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
    public function show(Trail $trail): TrailResource
    {
        return new TrailResource($trail->load(['images']));
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
    public function destroy(Request $request, string $id)
    {
        $trail = Trail::findOrFail($id);

        if ($request->user()->can('delete', $trail)) {
            $trail->delete();

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
