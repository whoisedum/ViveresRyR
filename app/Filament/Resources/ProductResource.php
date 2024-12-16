<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(50),
                Forms\Components\Select::make('categoria')
                    ->options([
                        'lacteos' => 'Lacteos',
                        'enlatadas' => 'Enlatadas',
                        'embutidos' => 'Embutidos',
                        'limpieza' => 'Limpieza',
                        'refrescos' => 'Refrescos',
                        'dulces' => 'Dulces',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                ->required()
                ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                ->searchable(),
                Tables\Columns\TextColumn::make('categoria')
                ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categoria')
                ->options([
                    'lacteos' => 'Lacteos',
                    'enlatadas' => 'Enlatadas',
                    'embutidos' => 'Embutidos',
                    'limpieza' => 'Limpieza',
                    'refrescos' => 'Refrescos',
                    'dulces' => 'Dulces',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('vender') 
                ->label('Vender')
                ->icon('heroicon-o-shopping-cart')
                ->form([
                    Forms\Components\TextInput::make('cantidad')
                        ->label('Cantidad a vender')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(fn (Product $record) => $record->cantidad), 
                ])
                ->action(function (array $data, Product $record) {
                    if ($record->cantidad >= $data['cantidad']) {
                        $record->cantidad -= $data['cantidad'];
                        $record->save();

                        \App\Models\Sale::create([
                            'product_id' => $record->id,
                            'cantidad' => $data['cantidad'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Venta registrada')
                            ->success()
                            ->body("Se vendieron {$data['cantidad']} unidades del producto {$record->nombre}.")
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Stock insuficiente')
                            ->warning()
                            ->body("No hay suficiente stock disponible para vender {$data['cantidad']} unidades. Stock actual: {$record->cantidad}.")
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->color('success'),
                Tables\Actions\Action::make('re_stock')
                ->label('Re-Stock')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Forms\Components\TextInput::make('cantidad')
                        ->label('Cantidad a agregar')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                ])
                ->action(function (array $data, Product $record) {
                    $record->cantidad += $data['cantidad'];
                    $record->save();

                    \App\Models\ReStock::create([
                        'product_id' => $record->id,
                        'cantidad' => $data['cantidad'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Re-Stock exitoso')
                        ->success()
                        ->body("Se añadieron {$data['cantidad']} unidades al stock del producto {$record->nombre}.")
                        ->send();
                })
                ->requiresConfirmation()
                ->color('info'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
