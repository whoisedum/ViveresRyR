<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReStockResource\Pages;
use App\Filament\Resources\ReStockResource\RelationManagers;
use App\Models\ReStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReStockResource extends Resource
{
    protected static ?string $model = ReStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'nombre')
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                    ->label('Cantidad Añadida')
                    ->required()
                    ->numeric()
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.nombre')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->label('Cantidad Añadida')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Re-Stock')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReStocks::route('/'),
            'create' => Pages\CreateReStock::route('/create'),
            'edit' => Pages\EditReStock::route('/{record}/edit'),
        ];
    }
}
