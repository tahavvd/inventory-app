<?php

namespace App\Filament\Admin\Resources\Inventory\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InventoryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('product.unit')
                    ->label('Unit')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('quantity')
            ->filters([
                SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')
                    ->relationship('product.category', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('low_stock')
                    ->label('Low stock only (≤ 10)')
                    ->query(fn($query) => $query->where('quantity', '<=', 10))
                    ->toggle(),
                Filter::make('out_of_stock')
                    ->label('Out of stock only')
                    ->query(fn($query) => $query->where('quantity', '<=', 0))
                    ->toggle(),
            ]);
    }
}
