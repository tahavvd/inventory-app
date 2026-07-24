<?php

namespace App\Filament\Admin\Resources\Inventory\Pages;

use App\Filament\Admin\Resources\Inventory\InventoryResource;
use Filament\Resources\Pages\ListRecords;

class ListInventory extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Browse-only: inventory is derived from Stock Transactions,
            // so there's no "create" action here.
        ];
    }
}
