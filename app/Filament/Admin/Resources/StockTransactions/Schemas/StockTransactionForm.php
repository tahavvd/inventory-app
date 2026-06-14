<?php

namespace App\Filament\Admin\Resources\StockTransactions\Schemas;

use App\Enums\StockTransactionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Product;
use App\Enums\ProductUnit;
use Filament\Forms\Components\Hidden;

class StockTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('type')
                    ->default(StockTransactionType::In->value),

                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),

                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $unit = Product::find($state)?->unit;
                        $set('unit', $unit?->value);
                        $set('quantity', null);
                    }),


                Hidden::make('unit')
                    ->dehydrated(false),

                TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->label('Quantity')
                    ->live()
                    ->step(fn($get) => in_array($get('unit'), [ProductUnit::Piece->value, ProductUnit::Box->value]) ? 1 : 0.01)
                    ->minValue(fn($get) => in_array($get('unit'), [ProductUnit::Piece->value, ProductUnit::Box->value]) ? 1 : 0.01)
                    ->rules(fn($get) => in_array($get('unit'), [ProductUnit::Piece->value, ProductUnit::Box->value]) ? ['integer', 'min:1'] : ['numeric', 'min:0.01'])
                    ->suffix(fn($get) => $get('unit')),

                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required()
                    ->native(false)
                    ->preload(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(5)
                    ->maxLength(500)
                    ->nullable()
                    ->columnSpanFull(),

                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }
}
