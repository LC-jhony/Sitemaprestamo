<?php

namespace App\Filament\Resources\FrecuencyResource\Pages;

use App\Filament\Resources\FrecuencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFrecuency extends EditRecord
{
    protected static string $resource = FrecuencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
