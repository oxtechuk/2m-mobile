<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('معلومات العميل')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('user_id')
                                ->label('حساب المستخدم')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->placeholder('شراء كزائر (بدون حساب)'),
                            TextInput::make('customer_name')
                                ->label('اسم العميل')
                                ->required(),
                            TextInput::make('customer_phone')
                                ->label('رقم الهاتف')
                                ->tel()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('customer_email')
                                ->label('البريد الإلكتروني')
                                ->email()
                                ->required(),
                            TextInput::make('order_number')
                                ->label('رقم الطلب')
                                ->required()
                                ->disabled()
                                ->placeholder('يتم إنشاؤه تلقائياً'),
                        ]),
                    ]),

                Section::make('تفاصيل الشحن')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('shipping_governorate')
                                ->label('المحافظة')
                                ->required(),
                            TextInput::make('shipping_city')
                                ->label('المدينة / المنطقة'),
                        ]),
                        Textarea::make('shipping_address')
                            ->label('العنوان التفصيلي')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('بيانات الدفع والحالة')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('payment_method')
                                ->label('طريقة الدفع')
                                ->options([
                                    'cod' => 'الدفع عند الاستلام',
                                    'instapay' => 'انستاباي (InstaPay)',
                                    'wallet' => 'محفظة إلكترونية (فودافون كاش / اتصالات كاش)',
                                ])
                                ->default('cod')
                                ->required(),
                            Select::make('payment_status')
                                ->label('حالة الدفع')
                                ->options([
                                    'pending' => 'قيد الانتظار',
                                    'paid' => 'مدفوع',
                                    'failed' => 'فشل الدفع',
                                    'refunded' => 'مسترجع',
                                ])
                                ->default('pending')
                                ->required(),
                            TextInput::make('payment_id')
                                ->label('معرف المعاملة (الرقم التعريفي)'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('حالة الطلب')
                                ->options([
                                    'pending' => 'قيد الانتظار',
                                    'processing' => 'قيد التحضير',
                                    'shipped' => 'تم الشحن',
                                    'delivered' => 'تم التوصيل',
                                    'cancelled' => 'ملغي',
                                ])
                                ->default('pending')
                                ->required(),
                            TextInput::make('coupon_code')
                                ->label('كوبون الخصم المستخدم'),
                        ]),
                        FileUpload::make('payment_screenshot')
                            ->label('صورة التحويل / إثبات الدفع')
                            ->image()
                            ->directory('payment_proofs')
                            ->columnSpanFull(),
                    ]),

                Section::make('تفاصيل الحساب المالي')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('subtotal')
                                ->label('المجموع الفرعي')
                                ->numeric()
                                ->required()
                                ->prefix('EGP'),
                            TextInput::make('shipping_fee')
                                ->label('رسوم الشحن')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->prefix('EGP'),
                            TextInput::make('discount_total')
                                ->label('قيمة الخصومات')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->prefix('EGP'),
                            TextInput::make('total')
                                ->label('الإجمالي الكلي')
                                ->numeric()
                                ->required()
                                ->prefix('EGP'),
                        ]),
                        Textarea::make('notes')
                            ->label('ملاحظات الطلب')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
