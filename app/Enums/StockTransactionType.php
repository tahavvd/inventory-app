<?php

namespace App\Enums;

enum StockTransactionType: string
{
    case In = 'in';
    case Out = 'out';
}
