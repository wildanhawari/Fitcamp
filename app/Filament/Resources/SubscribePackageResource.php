<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubscribePackage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubscribePackageResource\Pages;
use App\Filament\Resources\SubscribePackageResource\RelationManagers;

class SubscribePackageResource extends Resource
{
    protected static ?string $model = SubscribePackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(255),

                FileUpload::make('icon')
                ->required()
                ->image(),

                TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('IDR'),

                TextInput::make('duration')
                ->required()
                ->numeric()
                ->prefix('Days'),

                Repeater::make('subcribeBenefits')
                ->relationship('subcribeBenefits')
                ->schema([
                    TextInput::make('name')
                    ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon'),

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('duration')
                ->formatStateUsing(function (int $state): string {
                    $months = floor($state / 31); // Mengubah hari ke bulan dengan pembulatan ke bawah
                    return $months . ' month'; // Menampilkan hasil sebagai bilangan bulat
                }),

                TextColumn::make('price')
                ->money('idr',true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSubscribePackages::route('/'),
            'create' => Pages\CreateSubscribePackage::route('/create'),
            'edit' => Pages\EditSubscribePackage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return SubscribePackage::count() < 3; // Sembunyikan tombol create jika data >= 3
    }
}
