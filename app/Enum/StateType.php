<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum StateType: string implements HasLabel
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    public function getLabel(): ?string
    {
        return match ($this) {
            StateType::Active => 'Active',
            StateType::Inactive => 'Inactive',
        };
    }
}
