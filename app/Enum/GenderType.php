<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum GenderType: string implements HasLabel
{
    case Male = 'Male';
    case Female = 'Female';
    public function getLabel(): ?string
    {
        return match ($this) {
            GenderType::Male => 'Male',
            GenderType::Female => 'Female',
        };
    }
}
