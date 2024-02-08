<?php

namespace App\Enums;

enum CardStatusEnums: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case BLOCKED = 'BLOCKED';

}
