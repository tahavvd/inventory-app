<?php

namespace App\Filament\Admin\Resources\Inventory;

use App\Filament\Admin\Resources\Inventory\Pages\ListInventory;
use App\Filament\Admin\Resources\Inventory\Tables\InventoryTable;
use App\Models\Inventory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Inventory';

    protected static ?string $modelLabel = 'Inventory';

    // Inventory records are derived from Stock Transactions and should never
    // be created or edited directly — this resource is browse-only.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return InventoryTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventory::route('/'),
        ];
    }
}
