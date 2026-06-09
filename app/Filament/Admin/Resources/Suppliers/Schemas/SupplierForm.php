<?php

namespace App\Filament\Admin\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->placeholder('e.g. ABC Electronics'),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->placeholder('+213 555 123 456'),
                Textarea::make('address')
                    ->maxLength(500)
                    ->rows(3)
                    ->placeholder('Street, City, Country'),
            ]);
    }
}
