<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(OrderStatus $state): string => match ($state) {
                        OrderStatus::Pending => 'warning',
                        OrderStatus::Processing => 'info',
                        OrderStatus::Completed => 'success',
                        OrderStatus::Cancelled => 'danger',
                    }),
                TextColumn::make('total')
                    ->money('DZD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('advance')
                    ->label(fn($record) => match ($record->status) {
                        OrderStatus::Pending => 'Mark Processing',
                        OrderStatus::Processing => 'Mark Completed',
                        default => null,
                    })
                    ->icon('heroicon-o-arrow-right')
                    ->disabled(fn($record) => in_array($record->status, [OrderStatus::Completed, OrderStatus::Cancelled]))
                    ->action(fn($record) => $record->update([
                        'status' => match ($record->status) {
                            OrderStatus::Pending => OrderStatus::Processing->value,
                            OrderStatus::Processing => OrderStatus::Completed->value,
                        },
                    ])),

                Action::make('cancel')
                    ->label('Cancel Order')
                    ->color('danger')
                    ->disabled(fn($record) => in_array($record->status, [OrderStatus::Completed, OrderStatus::Cancelled]))
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'status' => OrderStatus::Cancelled,
                    ])),

                EditAction::make()
                    ->disabled(fn($record) => $record->status !== OrderStatus::Pending),


            ]);
    }
}
