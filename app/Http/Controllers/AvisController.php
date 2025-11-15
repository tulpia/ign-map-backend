<?php

namespace App\Http\Controllers;

use App\Http\Requests\Avis\AvisStoreRequest;
use App\Http\Requests\Avis\AvisUpdateRequest;
use App\Models\Avis;
use App\Models\Trail;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(AvisStoreRequest $request, Trail $trail): JsonResponse
    {
        $avis = new Avis($request->validated());

        // On associe le tout
        $avis->trail()->associate($trail);
        $avis->user()->associate($request->user());
        $avis->save();

        return response()->json(['message' => 'Avis ajouté.'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AvisUpdateRequest $request, Trail $trail, Avis $avi): JsonResponse
    {
        if ($request->user()->can('update', $avi)) {
            $avi->update($request->validated());

            return response()->json(['message' => 'Trail updated successfully']);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trail $trail, Avis $avi, Request $request): JsonResponse|Response
    {
        if ($request->user()->can('delete', $avi)) {
            Avis::destroy($avi->id);

            return response()->noContent();
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
