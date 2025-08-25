<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Toggle::make('is_default')->label('Default Plan'),
                Forms\Components\TextInput::make('price')->required()->numeric()->prefix('$'),
                Forms\Components\TextInput::make('daily_courier_limit')->required()->numeric(),
                Forms\Components\TextInput::make('monthly_courier_limit')->required()->numeric(),
                Forms\Components\TextInput::make('monthly_incomplete_order_limit')->required()->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('price')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('daily_courier_limit')->numeric()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('set_as_default')
                    ->label('Set as Default')
                    ->icon('heroicon-o-star')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Plan $record) {
                        DB::transaction(function () use ($record) {
                            Plan::where('is_default', true)->update(['is_default' => false]);
                            $record->update(['is_default' => true]);
                        });
                        Notification::make()
                            ->title("'{$record->name}' is now the default plan.")
                            ->success()->send();
                    })
                    ->hidden(fn (Plan $record): bool => $record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    // *** মূল পরিবর্তনটি এখানে ***
    // এই ফাংশনটি এখন সঠিকভাবে পেজের রাউটগুলো রিটার্ন করছে
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }    
}