<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Enums\ProductUnit;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        TextInput::make('address')
                            ->maxLength(500),
                    ]),
                Repeater::make('items')
                    ->relationship('items')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('unit_price', $product->selling_price);
                                    $set('unit', $product->unit->value);
                                }
                            }),
                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->live()
                            ->step(fn($get) => in_array(
                                $get('unit'),
                                [ProductUnit::Piece->value, ProductUnit::Box->value]
                            ) ? 1 : 0.01)
                            ->minValue(fn($get) => in_array(
                                $get('unit'),
                                [ProductUnit::Piece->value, ProductUnit::Box->value]
                            ) ? 1 : 0.01)
                            ->rules(fn($get) => in_array(
                                $get('unit'),
                                [ProductUnit::Piece->value, ProductUnit::Box->value]
                            ) ? ['integer', 'min:1'] : ['numeric', 'min:0.01'])
                            ->rule(function ($get) {
                                return function (String $attribute, $value, \Closure $fail) use ($get) {
                                    $inventory = \App\Models\Inventory::where(
                                        'product_id',
                                        $get('product_id')
                                    )->where(
                                        'warehouse_id',
                                        $get('warehouse_id')
                                    )->first();

                                    $available = $inventory?->quantity ?? 0;

                                    if ($value > $available) {
                                        if ($available == 0) {
                                            $fail('The selected product is out of stock.');
                                        } else {
                                            $fail('The selected product has only ' . $available . ' available in the selected warehouse.');
                                        }
                                    }
                                };
                            }),

                        TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->prefix('DZD')
                            ->readOnly()
                            ->minValue(0),
                        TextInput::make('unit')
                            ->readOnly()
                            ->prefix('Unit:')
                            ->placeholder('Select a product first')
                            ->dehydrated(false),
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->required()
                            ->native(false)
                            ->options(function ($get) {
                                $productId = $get('product_id');
                                if (!$productId) return [];

                                return \App\Models\Inventory::query()
                                    ->where('product_id', $productId)
                                    ->where('quantity', '>', 0)
                                    ->with('warehouse')
                                    ->get()
                                    ->mapWithKeys(fn($inventory) => [
                                        $inventory->warehouse_id => $inventory->warehouse->name . ' (' . $inventory->quantity . ' available)'
                                    ]);
                            }),
                    ])
                    ->columns(5)
                    ->addActionLabel('Add Product')
                    ->minItems(1),
            ]);
    }
}
