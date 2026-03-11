<?php

namespace App\Actions\Avis;

use App\Models\Avis;

class UpdateAvisAction
{
    /**
     * Execute the action to update an existing Avis.
     *
     * @param Avis $avis
     * @param array $data
     * @return void
     */
    public function execute(Avis $avis, array $data): void
    {
        $avis->update($data);
    }
}
