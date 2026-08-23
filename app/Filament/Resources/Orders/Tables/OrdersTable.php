<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('اسم العميل')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                TextColumn::make('shipping_governorate')
                    ->label('المحافظة')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod' => 'عند الاستلام',
                        'instapay' => 'انستاباي',
                        'wallet' => 'محفظة إلكترونية',
                        default => $state,
                    }),
                TextColumn::make('payment_status')
                    ->label('حالة الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'مدفوع',
                        'pending' => 'قيد الانتظار',
                        'failed' => 'فشل الدفع',
                        'refunded' => 'مسترجع',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('حالة الطلب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'delivered' => 'success',
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد التحضير',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التوصيل',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime()
                    ->sortable(),
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
