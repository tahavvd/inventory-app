<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

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
                        Hidden::make('id')->dehydrated(false), // lets us know which OrderItem this is, when editing

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
                                [ProductUnit::Kg->value, ProductUnit::Liter->value]
                            ) ? 0.01 : 1)
                            ->minValue(fn($get) => in_array(
                                $get('unit'),
                                [ProductUnit::Kg->value, ProductUnit::Liter->value]
                            ) ? 0.01 : 1)
                            ->rules(fn($get) => in_array(
                                $get('unit'),
                                [ProductUnit::Kg->value, ProductUnit::Liter->value]
                            ) ? ['numeric', 'min:0.01'] : ['integer', 'min:1'])
                            ->rule(function ($get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $inventory = \App\Models\Inventory::where(
                                        'product_id',
                                        $get('product_id')
                                    )->where(
                                        'warehouse_id',
                                        $get('warehouse_id')
                                    )->first();

                                    $available = $inventory?->quantity ?? 0;

                                    // If we're editing an existing order item, and the product/warehouse
                                    // haven't changed, the stock it already "used" should count as available too.
                                    $itemId = $get('id');
                                    if ($itemId) {
                                        $originalItem = \App\Models\OrderItem::find($itemId);
                                        if (
                                            $originalItem
                                            && $originalItem->product_id == $get('product_id')
                                            && $originalItem->warehouse_id == $get('warehouse_id')
                                        ) {
                                            $available += $originalItem->quantity;
                                        }
                                    }

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
                            ->dehydrated(false),
                        Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->relationship('warehouse', 'name')
                            ->required()
                            ->native(false)
                            ->options(function ($get) {
                                $productId = $get('product_id');
                                if (!$productId) return [];

                                $itemId = $get('id');
                                $originalItem = $itemId ? \App\Models\OrderItem::find($itemId) : null;

                                return \App\Models\Inventory::query()
                                    ->where('product_id', $productId)
                                    ->with('warehouse')
                                    ->get()
                                    ->map(function ($inventory) use ($originalItem) {
                                        $available = $inventory->quantity;
                                        if ($originalItem && $originalItem->warehouse_id == $inventory->warehouse_id) {
                                            $available += $originalItem->quantity;
                                        }
                                        $inventory->available = $available;
                                        return $inventory;
                                    })
                                    ->filter(fn($inventory) => $inventory->available > 0)
                                    ->mapWithKeys(fn($inventory) => [
                                        $inventory->warehouse_id => $inventory->warehouse->name . ' (' . $inventory->available . ' available)'
                                    ]);
                            }),
                    ])
                    ->columns(5)
                    ->addActionLabel('Add Product')
                    ->minItems(1),
            ]);
    }
}
