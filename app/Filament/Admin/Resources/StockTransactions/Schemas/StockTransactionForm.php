<?php

namespace App\Filament\Admin\Resources\StockTransactions\Schemas;

use App\Enums\StockTransactionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('supplier_id')
                    ->relationship('supplier', 'id'),
                Select::make('order_id')
                    ->relationship('order', 'id'),
                Select::make('type')
                    ->options(StockTransactionType::class)
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
