<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('اسم المنتج')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('رمز المنتج (SKU)')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('السعر الأساسي')
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('سعر الخصم')
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'physical' => 'مادي',
                        'digital' => 'شيت رقمي (PDF)',
                        'course' => 'كورس',
                        'session' => 'جلسة/خدمة',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'physical' => 'info',
                        'digital' => 'success',
                        'course' => 'warning',
                        'session' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('stock')
                    ->label('المخزون')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('badge')
                    ->label('الشارة المعروضة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
