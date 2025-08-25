<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('websites');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Forms\Components\Toggle::make('is_admin')->label('Is Admin?')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                
                // *** মূল পরিবর্তন (আপনার পরামর্শ অনুযায়ী) ***
                // "Current Plan" কলামটি এখন একটি ক্লিকযোগ্য ড্রপডাউন
                Tables\Columns\SelectColumn::make('subscription.plan_id')
                    ->label('Current Plan')
                    ->options(Plan::all()->pluck('name', 'id'))
                    ->updateStateUsing(function ($record, $state) {
                        $record->subscription()->updateOrCreate(
                            ['user_id' => $record->id],
                            ['plan_id' => $state]
                        );
                         Notification::make()
                            ->title('Plan changed successfully')
                            ->success()
                            ->send();
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy(
                                Plan::select('name')->whereColumn('plans.id', 'subscriptions.plan_id'),
                                $direction
                            );
                    }),

                Tables\Columns\TextColumn::make('websites_count')
                    ->label('Websites')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('smsCredit.balance')
                    ->label('SMS Credits')
                    ->numeric()
                    ->sortable()
                    ->default(0),
                
                Tables\Columns\ToggleColumn::make('is_admin')->label('Is Admin?'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('add_sms_credits')
                    ->label('Add Credits')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount to Add')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (User $user, array $data): void {
                        $amount = intval($data['amount']);
                        $user->smsCredit()->firstOrCreate(['user_id' => $user->id])
                             ->increment('balance', $amount);

                        Notification::make()
                            ->title("Successfully added {$amount} credits to {$user->name}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}