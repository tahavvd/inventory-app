<?php

namespace App\Enums;

enum ProductUnit : string
{
    case Piece = 'piece';
    case Kg = 'kg';
    case Liter = 'liter';
    case Box = 'box';
    // add whatever units your business uses
}
