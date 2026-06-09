<?php

namespace App\Filament\Admin\Resources\StockTransactions\Pages;

use App\Filament\Admin\Resources\StockTransactions\StockTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockTransaction extends EditRecord
{
    protected static string $resource = StockTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
