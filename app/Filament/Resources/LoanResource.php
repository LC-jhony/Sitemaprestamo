<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('rate_id')
                            ->relationship('rate', 'percent')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('years')
                            ->nullable()
                            ->numeric()
                            ->extraInputAttributes(['width' => 10]),
                        Forms\Components\Select::make('frecuency_id')
                            ->relationship('frecuency', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('Amort_method')
                            ->options([
                                'french' => 'French (Equal Payments)',
                                'german' => 'German (Equal Principal)',
                                'american' => 'American (Interest Only)'
                            ])
                            ->required()
                            ->reactive()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, Forms\Get $get) {
                                if ($state === 'french') {
                                    $amount = $get('amount');
                                    $rate = $get('rate_id') / 100;
                                    $years = $get('years');
                                    $frequency = $get('frecuency_id');

                                    $set('payments', self::calculateFrenchAmortization($amount, $rate, $years, $frequency));
                                }
                            }),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->hidden()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->dehydrated(true)
                            ->native(false),


                    ])->columns(6),

                TableRepeater::make('payments')
                    ->headers([
                        Header::make('date')->width('150px')
                            ->label('Fecha'),
                        Header::make('pay')->width('150px')
                            ->label('Pago'),
                        Header::make('amort')->width('150px')
                            ->label('Amortización'),
                        Header::make('interest')->width('150px')
                            ->label('Interés'),
                        Header::make('balance')->width('150px')
                            ->label('Balance'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('date'),
                        Forms\Components\TextInput::make('pay'),
                        Forms\Components\TextInput::make('amort'),
                        Forms\Components\TextInput::make('interest'),
                        Forms\Components\TextInput::make('balance'),
                    ])

                    ->defaultItems(0)
                    ->reorderable()
                    ->cloneable()
                    ->collapsible()
                    ->minItems(3)
                    ->maxItems(5)
                    ->columnSpan('full'),
            ]);
    }
    private static function calculateFrenchAmortization($amount, $rate, $years, $frequency): array
    {
        $n = $years * $frequency; // Número total de pagos
        $i = $rate; // Tasa de interés por período
        $payment = $amount * ($i * pow(1 + $i, $n)) / (pow(1 + $i, $n) - 1); // Cuota fija

        $schedule = [];
        $balance = $amount;

        for ($t = 1; $t <= $n; $t++) {
            $interest = $balance * $i;
            $amortization = $payment - $interest;
            $balance = $balance - $amortization;

            if ($t == $n) {
                $amortization = $balance;
                $payment = $amortization + $interest;
                $balance = 0;
            }

            $schedule[] = [
                'date' => now()->addMonths($t)->format('Y-m-d'),
                'pay' => number_format($payment, 2, '.', ''),
                'amort' => number_format($amortization, 2, '.', ''),
                'interest' => number_format($interest, 2, '.', ''),
                'balance' => number_format($balance, 2, '.', ''),
            ];
        }

        return $schedule;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('frecuency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
