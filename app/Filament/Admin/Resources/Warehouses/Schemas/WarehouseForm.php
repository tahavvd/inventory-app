<?php

namespace App\Filament\Admin\Resources\Warehouses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. Main Warehouse'),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Annaba, Algeria'),
            ]);
    }
}
