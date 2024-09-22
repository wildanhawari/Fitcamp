<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\SubscribePackage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use App\Models\SubscribeTransaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubscribeTransactionResource\Pages;
use App\Filament\Resources\SubscribeTransactionResource\RelationManagers;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;

class SubscribeTransactionResource extends Resource
{
    protected static ?string $model = SubscribeTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Product and Price')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            Select::make('subscribe_package_id')
                            ->relationship('subscribePackage','name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $subcribePackage = SubscribePackage::find($state);
                                $price = $subcribePackage ? $subcribePackage->price : 0;
                                $duration = $subcribePackage ? $subcribePackage->duration : 0;

                                $set('price',number_format($price,0,',','.'));
                                $set('duration', $duration);

                                $tax = 0.11;
                                $totalTaxAmount = $tax * $price;

                                $totalAmount = $totalTaxAmount + $price;
                                $set('total_amount', $totalAmount);
                                $set('total_tax_amount', number_format($totalTaxAmount,0,',','.'));
                            })
                            ->afterStateHydrated(function (callable $get, callable $set, $state) {
                                $subcribePackageId = $state;
                                if ($subcribePackageId) {
                                    $subcribePackage = SubscribePackage::find($subcribePackageId);
                                    $price = $subcribePackage ? $subcribePackage->price : 0;
                                    $set('price', $price);

                                    $tax = 0.11;
                                    $totalTaxAmount = $tax * $price;
                                    $set('total_tax_amount', number_format($totalTaxAmount,0,',','.'));
                                }
                            }),

                            TextInput::make('price')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            TextInput::make('total_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            TextInput::make('total_tax_amount')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('IDR'),

                            DatePicker::make('started_at')
                            ->required(),

                            DatePicker::make('ended_at')
                            ->required(),

                            TextInput::make('duration')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->prefix('Days'),
                        ])
                    ]),

                    Step::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('phone')
                            ->required()
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                            TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        ]),
                    ]),    

                    Step::make('Payment Information')
                    ->description('')
                    ->schema([
                        TextInput::make('booking_trx_id')
                        ->required()
                        ->maxLength(255),

                        ToggleButtons::make('is_paid')
                        ->label('Apakah sudah membayar?')
                        ->boolean()
                        ->grouped()
                        ->icons([
                            true => 'heroicon-o-pencil',
                            false => 'heroicon-o-clock',
                        ])
                        ->required(),

                        FileUpload::make('proof')
                        ->required()
                        ->image(),
                    ]),

                ])
                ->columnSpanFull()
                ->columns(1)
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscribePackage.name'),

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('total_amount')
                ->money(currency: 'idr', divideBy:true),

                TextColumn::make('booking_trx_id')
                ->copyable()
                ->copyMessage('Code Copied!')
                ->copyMessageDuration(1500),
                
                IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverifikasi'),
            ])
            ->filters([
                SelectFilter::make('subscribe_package_id')
                ->label('Subscribe Package')
                ->relationship('subscribePackage', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('aprove')
                ->label('Approve')
                ->action(function (SubscribeTransaction $record) {
                    $record->is_paid = true;
                    $record->save();

                    // Notification
                    Notification::make()
                    ->title('Transaction Approved')
                    ->success()
                    ->body('The transaction has been successfully approved.')
                    ->send();
                })
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (SubscribeTransaction $record) => !$record->is_paid),
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
            'index' => Pages\ListSubscribeTransactions::route('/'),
            'create' => Pages\CreateSubscribeTransaction::route('/create'),
            'edit' => Pages\EditSubscribeTransaction::route('/{record}/edit'),
        ];
    }
}
