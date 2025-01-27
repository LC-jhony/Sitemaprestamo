<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum PymentType: string implements HasLabel
{
    case ONTIME = 'ONTIME';
    case LATE = 'LATE';
    public function getLabel(): ?string
    {
        return match ($this) {
            PymentType::ONTIME => 'ONTIME',
            PymentType::LATE => 'LATE',
        };
    }
}
