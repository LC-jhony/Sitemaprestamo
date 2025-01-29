<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

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
                            ->default(5000)
                            ->numeric(),
                        Forms\Components\Select::make('rate_id')
                            ->relationship('rate', 'percent', fn(Builder $query) => $query
                                ->selectRaw('*, FORMAT(percent, 0) as percent'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('years')
                            ->default(1)
                            ->numeric()
                            ->extraInputAttributes(['width' => 10]),
                        Forms\Components\Select::make('frecuency_id')
                            ->relationship('frecuency', 'days')
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
                            ->reactive(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->hidden()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->dehydrated(true)
                            ->native(false),


                    ])->columns(6)
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        if ($get('Amort_method') !== 'french') {
                            return;
                        }

                        $amount = $get('amount');
                        $rate = $get('rate_id');
                        $years = $get('years');
                        $frecuency = $get('frecuency_id');

                        $payments = self::calculateFrenchAmortization($amount, $rate, $years, $frecuency);
                        $set('payments', $payments);
                    }),

                TableRepeater::make('payments')
                    ->headers([
                        Header::make('date')->width('150px'),
                        Header::make('pay')->width('150px'),
                        Header::make('amort')->width('150px'),
                        Header::make('interest')->width('150px'),
                        Header::make('balance')->width('150px'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('date'),
                        MoneyInput::make('pay')
                            ->decimals(2),
                        MoneyInput::make('amort')
                            ->decimals(2),
                        MoneyInput::make('interest')
                            ->decimals(2),
                        MoneyInput::make('balance')
                            ->decimals(2),
                    ])
                    ->defaultItems(0)
                    ->reorderable()
                    ->cloneable()
                    ->collapsible()
                    ->minItems(3)
                    ->maxItems(5)
                    ->columnSpan('full')
                    ->visible(fn(Forms\Get $get) => $get('Amort_method') === 'french')


            ]);
    }
    private static function calculateFrenchAmortization($amount, $annualRate, $years, $frequency)
    {
        $totalPayments = $years * 12; // Total number of payments (monthly)
        $monthlyRate = $annualRate / 12 / 100; // Convert annual rate to monthly decimal
        $fixedAmortization = $amount / $totalPayments; // Fixed amortization per period

        $payments = []; // Initialize the array to hold payment details
        $remainingBalance = $amount; // Start with the full loan amount

        for ($i = 1; $i <= $totalPayments; $i++) {
            // Calculate interest for the current period
            $interestForPeriod = $remainingBalance * $monthlyRate;

            // Total payment for the current period
            $totalPayment = $fixedAmortization + $interestForPeriod;

            // Update remaining balance after payment
            $remainingBalance -= $fixedAmortization;

            // Store the payment details in the array
            $payments[] = [
                'date' => now()->addMonths($i)->format('Y-m-d'),
                'pay' => number_format($totalPayment, 2),
                'amort' => number_format($fixedAmortization, 2),
                'interest' => number_format($interestForPeriod, 2),
                'balance' => number_format($remainingBalance, 2),
            ];
        }

        return $payments; // Return the array of payment details
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
