<?php

namespace App\Filament\Resources\ReStockResource\Pages;

use App\Filament\Resources\ReStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReStock extends EditRecord
{
    protected static string $resource = ReStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
