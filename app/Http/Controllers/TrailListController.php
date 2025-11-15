<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrailList\StoreTrailListRequest;
use App\Http\Requests\TrailList\UpdateTrailListRequest;
use App\Http\Resources\TrailListResource;
use App\Models\TrailList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrailListController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $trailslists = $request->user()->lists()->get()->all();

        return TrailListResource::collection($trailslists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrailListRequest $request)
    {
        $list = new TrailList($request->validated());

        // Je ne gère pas l'assignation des trails ici, ca ne sert a rien
        return $request->user()->lists()->create($list->attributesToArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $trailList = TrailList::findOrFail($id);

        if ($request->user()->can('view', $trailList)) {
            return new TrailListResource($trailList);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrailListRequest $request, string $id)
    {
        $trailList = TrailList::findOrFail($id);

        if ($trailList && $request->user()->can('update', $trailList)) {
            $trailList->update($request->safe()->only(['name']));

            // Gestion des associations ici
            $trailIds = array_map('intval', $request->safe()->only(['trails'])['trails']);
            $trailList->trails()->syncWithoutDetaching($trailIds);

            return response()->json(['message' => 'List updated successfully.']);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrailList $trailList, Request $request)
    {
        if ($request->user()->can('delete', $trailList)) {
            TrailList::destroy($trailList->id);

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
