<?php

namespace App\Filament\Resources\ReStockResource\Pages;

use App\Filament\Resources\ReStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReStocks extends ListRecords
{
    protected static string $resource = ReStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
