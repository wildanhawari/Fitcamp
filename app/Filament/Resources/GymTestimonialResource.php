<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\GymTestimonial;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GymTestimonialResource\Pages;
use App\Filament\Resources\GymTestimonialResource\RelationManagers;

class GymTestimonialResource extends Resource
{
    protected static ?string $model = GymTestimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->maxLength(255),

                TextInput::make('occupation')
                ->required()
                ->maxLength(255),

                FileUpload::make('photo')
                ->image()
                ->required(),

                Select::make('gym_id')
                ->relationship('gym','name')
                ->searchable()
                ->preload()
                ->required(),

                Textarea::make('message')
                ->required()
                ->rows(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo'),

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('gym.name'),

            ])
            ->filters([
                SelectFilter::make('gym_id')
                ->label('Gym')
                ->relationship('gym','name'),
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
            'index' => Pages\ListGymTestimonials::route('/'),
            'create' => Pages\CreateGymTestimonial::route('/create'),
            'edit' => Pages\EditGymTestimonial::route('/{record}/edit'),
        ];
    }
}
