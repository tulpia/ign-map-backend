<?php

namespace App\Http\Controllers;

use App\Actions\Trail\StoreTrailAction;
use App\Actions\Trail\UpdateTrailAction;
use App\Http\Requests\Trail\TrailStoreRequest;
use App\Http\Requests\Trail\TrailUpdateRequest;
use App\Http\Resources\TrailResource;
use App\Models\Trail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Controller for handling Trail related API requests.
 */
class TrailController extends Controller
{
    public function __construct(
        private StoreTrailAction $storeTrailAction,
        private UpdateTrailAction $updateTrailAction
    ) {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of trails with advanced filtering.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $trails = QueryBuilder::for(Trail::class)
            ->allowedFilters([
                'title',
                'difficulty',
                AllowedFilter::callback('distance', function (Builder $query, $value) {
                    $query->where('distance', '<=', $value);
                }),
                AllowedFilter::callback('denivele', function (Builder $query, $value) {
                    $query->where('denivele', '<=', $value);
                }),
                AllowedFilter::callback('time_to_complete', function (Builder $query, $value) {
                    $query->where('time_to_complete', '<=', $value);
                }),
            ])
            ->when($request->has(['lat_min', 'lat_max', 'lng_min', 'lng_max']), function (Builder $query) use ($request) {
                $query->whereBetween('latitude', [$request->lat_min, $request->lat_max])
                      ->whereBetween('longitude', [$request->lng_min, $request->lng_max]);
            })
            ->allowedIncludes(['images', 'avis'])
            ->with(['images', 'avis'])
            ->withAvg('avis', 'note')
            ->paginate(12);

        return TrailResource::collection($trails);
    }

    /**
     * Store a newly created trail.
     * @param TrailStoreRequest $request
     * @return TrailResource
     */
    public function store(TrailStoreRequest $request): TrailResource
    {
        $trail = $this->storeTrailAction->execute(
            $request->user(),
            $request->validated()
        );

        return new TrailResource($trail);
    }

    /**
     * Display the specified trail.
     * @param Trail $trail
     * @return TrailResource
     */
    public function show(Trail $trail): TrailResource
    {
        return new TrailResource($trail->load(['images', 'avis'])->loadAvg('avis', 'note'));
    }

    /**
     * Update the specified trail.
     * @param TrailUpdateRequest $request
     * @param string $id
     * @return TrailResource|\Illuminate\Http\JsonResponse
     */
    public function update(TrailUpdateRequest $request, string $id)
    {
        $trail = Trail::findOrFail($id);

        if ($request->user()->can('update', $trail)) {
            $trail = $this->updateTrailAction->execute($trail, $request->validated());

            return new TrailResource($trail);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified trail.
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
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
