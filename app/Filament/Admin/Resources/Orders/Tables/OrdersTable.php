<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->selectable()
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
                Action::make('changeStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->required()
                            ->native(false),
                    ])
                    ->fillForm(fn($record) => ['status' => $record->status])
                    ->action(fn($record, array $data) => $record->update(['status' => $data['status']])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
