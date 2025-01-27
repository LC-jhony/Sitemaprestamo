<?php

namespace App\Filament\Resources\RateResource\Pages;

use App\Filament\Resources\RateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRate extends ViewRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
