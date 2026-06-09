<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Order Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order #'),
                        TextEntry::make('customer.name')
                            ->label('Customer'),
                        TextEntry::make('user.name')
                            ->label('Created By'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state->value) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            }),
                        TextEntry::make('total')
                            ->money('DZD'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('d M Y, H:i'),
                    ]),

                Section::make('Order Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),
                                TextEntry::make('product.unit')
                                    ->label('Unit')
                                    ->badge(),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                                TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->money('DZD'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('DZD')
                                    ->getStateUsing(fn($record) => $record->quantity * $record->unit_price),
                            ])
                            ->columns(5),
                    ]),

                Section::make('Status History')
                    ->schema([
                        RepeatableEntry::make('statusHistories')
                            ->label('')
                            ->schema([
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn($state) => match ($state->value) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    }),
                                TextEntry::make('user.name')
                                    ->label('Changed By'),
                                TextEntry::make('created_at')
                                    ->label('Changed At')
                                    ->dateTime('d M Y, H:i'),
                            ])
                            ->columns(3),
                    ]),
            ])->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
