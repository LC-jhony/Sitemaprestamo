<?php

namespace App\Filament\Resources\FrecuencyResource\Pages;

use App\Filament\Resources\FrecuencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFrecuencies extends ListRecords
{
    protected static string $resource = FrecuencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
