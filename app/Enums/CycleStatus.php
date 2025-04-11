<?php

namespace App\Enums;

enum CycleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Closed = 'closed';
}
