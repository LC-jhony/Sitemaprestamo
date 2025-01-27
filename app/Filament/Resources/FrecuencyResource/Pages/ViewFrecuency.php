<?php

namespace App\Filament\Resources\FrecuencyResource\Pages;

use App\Filament\Resources\FrecuencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFrecuency extends ViewRecord
{
    protected static string $resource = FrecuencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
