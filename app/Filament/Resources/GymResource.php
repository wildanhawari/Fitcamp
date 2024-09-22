<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GymResource\Pages;
use App\Filament\Resources\GymResource\RelationManagers;
use App\Models\Facility;
use App\Models\Gym;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GymResource extends Resource
{
    protected static ?string $model = Gym::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Fieldset::make('Details')
                ->schema([

                    TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                    Textarea::make('address')
                    ->rows(3)
                    ->required()
                    ->maxLength(255),

                    FileUpload::make('thumbnail')
                    ->required()
                    ->image(),

                    Repeater::make('gymPhotos')
                    ->relationship('gymPhotos')
                    ->schema([
                        FileUpload::make('photo')
                        ->required()
                        ->image(),
                    ]),
                ]),

                Fieldset::make('Additional')
                ->schema([
                    Textarea::make('about')
                    ->rows(6)
                    ->required(),

                    Repeater::make('gymFacilities')
                    ->relationship('gymFacilities')
                    ->schema([
                        Select::make('facility_id')
                        ->label('Gym Facility')
                        ->options(Facility::all()->pluck('name','id'))
                        ->searchable()
                        ->required(),
                    ]),

                    Select::make('is_popular')
                    ->options([
                        true => 'Popular',
                        false => 'Not Popular',
                    ])
                    ->required(),

                    Select::make('city_id')
                    ->relationship('city','name')
                    ->searchable()
                    ->preload()
                    ->required(),

                    TimePicker::make('open_time_at')
                    ->required(),

                    TimePicker::make('closed_time_at')
                    ->required(),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('city.name'),

                ImageColumn::make('thumbnail'),

                IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular'),
            ])
            ->filters([
                SelectFilter::make('city_id')
                ->label('city')
                ->relationship('city','name'),
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
            'index' => Pages\ListGyms::route('/'),
            'create' => Pages\CreateGym::route('/create'),
            'edit' => Pages\EditGym::route('/{record}/edit'),
        ];
    }
}
