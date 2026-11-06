<?php

namespace App\Enums;

enum BookOrientation: string
{
    case Portrait = 'portrait';
    case Landscape = 'landscape';
    case Square = 'square';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
