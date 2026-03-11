<?php

namespace App\Http\Controllers;

use App\Actions\TrailList\StoreTrailListAction;
use App\Actions\TrailList\UpdateTrailListAction;
use App\Http\Requests\TrailList\StoreTrailListRequest;
use App\Http\Requests\TrailList\UpdateTrailListRequest;
use App\Http\Resources\TrailListResource;
use App\Models\TrailList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrailListController extends Controller
{
    public function __construct(
        private StoreTrailListAction $storeTrailListAction,
        private UpdateTrailListAction $updateTrailListAction
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $trailslists = $request->user()->lists()->with('trails.images')->get();

        return TrailListResource::collection($trailslists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrailListRequest $request)
    {
        // Je ne gère pas l'assignation des trails ici, ca ne sert a rien
        return $this->storeTrailListAction->execute(new TrailList($request->validated()), $request->user());
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
            $this->updateTrailListAction->execute($trailList, $request->validated());

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
