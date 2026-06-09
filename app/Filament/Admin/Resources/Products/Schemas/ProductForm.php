<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Enums\ProductUnit;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->required(),
                Select::make('unit')
                    ->options(ProductUnit::class)
                    ->required()
                    ->native(false),
                TextInput::make('selling_price')
                    ->required()
                    ->numeric()
                    ->prefix('DZD')
                    ->minValue(0),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->prefix('DZD')
                    ->minValue(0),
            ]);
    }
}
