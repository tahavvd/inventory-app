<?php

namespace App\Filament\Admin\Resources\Orders\Pages;

use App\Filament\Admin\Resources\Orders\OrderResource;
use Filament\Resources\Pages\EditRecord;
use App\Enums\OrderStatus;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $total = $this->record->items()
            ->sum(\DB::raw('quantity * unit_price'));

        $this->record->update(['total' => $total]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->record->status !== OrderStatus::Pending) {
            $this->redirect(
                $this->getResource()::getUrl('view', ['record' => $this->record])
            );
        }
    }
}
