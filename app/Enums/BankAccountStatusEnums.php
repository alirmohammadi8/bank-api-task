<?php

namespace App\Enums;

enum BankAccountStatusEnums: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case BLOCKED = 'BLOCKED';

}
