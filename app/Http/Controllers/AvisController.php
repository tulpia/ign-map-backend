<?php

namespace App\Http\Controllers;

use App\Actions\Avis\StoreAvisAction;
use App\Actions\Avis\UpdateAvisAction;
use App\Http\Requests\Avis\AvisStoreRequest;
use App\Http\Requests\Avis\AvisUpdateRequest;
use App\Models\Avis;
use App\Models\Trail;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function __construct(
        private StoreAvisAction $storeAvisAction,
        private UpdateAvisAction $updateAvisAction
    ) {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    /**
     * Store a newly created resource in storage.
     * @param AvisStoreRequest $request
     * @param Trail $trail
     * @return JsonResponse
     */
    public function store(AvisStoreRequest $request, Trail $trail): JsonResponse
    {
        $this->storeAvisAction->execute(new Avis($request->validated()), $trail, $request->user());

        return response()->json(['message' => 'Avis ajouté.'], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param AvisUpdateRequest $request
     * @param Avis $avis
     * @return JsonResponse
     */
    public function update(AvisUpdateRequest $request, Avis $avis): JsonResponse
    {
        if ($request->user()->can('update', $avis)) {
            $this->updateAvisAction->execute($avis, $request->validated());

            return response()->json(['message' => 'Avis updated successfully']);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     * @param Avis $avis
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function destroy(Avis $avis, Request $request): JsonResponse|Response
    {
        if ($request->user()->can('delete', $avis)) {
            Avis::destroy($avis->id);

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
