<?php

namespace App\Filament\Admin\Resources\StockTransactions\Tables;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use App\Enums\StockTransactionType;


class StockTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Transaction #')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order.customer.name')
                    ->label('Customer')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        StockTransactionType::In->value  => 'In',
                        StockTransactionType::Out->value => 'Out',
                    ])
                    ->placeholder('All'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent);
    }
}
