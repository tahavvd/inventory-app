<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                Textarea::make('address')
                    ->maxLength(500)
                    ->rows(3),
            ]);
    }
}
