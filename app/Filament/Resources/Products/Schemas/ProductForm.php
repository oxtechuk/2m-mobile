<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->description('أدخل البيانات الأساسية للمنتج أو الباقة')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('اسم المنتج')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('الرابط الفريد (Slug)')
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('type')
                                ->label('نوع المنتج')
                                ->options([
                                    'physical' => 'منتج مادي (أداة/لعبة)',
                                    'digital' => 'شيت رقمي (PDF)',
                                    'course' => 'كورس تدريبي',
                                    'session' => 'جلسة / خدمة',
                                ])
                                ->default('physical')
                                ->required(),
                            TextInput::make('badge')
                                ->label('شارة مميزة (Badge)')
                                ->placeholder('مثال: الأكثر مبيعاً، جديد، باقة توفير'),
                            Toggle::make('is_active')
                                ->label('نشط')
                                ->default(true)
                                ->inline(false),
                        ]),
                        Textarea::make('short_description')
                            ->label('وصف بيعي مختصر (يظهر أعلى الصفحة)')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('الوصف الكامل للمنتج')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Section::make('التسعير والمخزون')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('price')
                                ->label('السعر الأساسي')
                                ->numeric()
                                ->required()
                                ->prefix('EGP'),
                            TextInput::make('sale_price')
                                ->label('سعر الخصم')
                                ->numeric()
                                ->prefix('EGP'),
                            TextInput::make('sku')
                                ->label('رمز المنتج (SKU)')
                                ->placeholder('مثال: PHY-SCB-01'),
                            TextInput::make('stock')
                                ->label('المخزون')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ]),
                    ]),

                Section::make('التصنيفات والروابط')
                    ->description('ربط المنتج بالمهارات والسن والاحتياج لتسهيل البحث على الآباء')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('categories')
                                ->label('الأقسام الرئيسية')
                                ->multiple()
                                ->relationship('categories', 'name')
                                ->preload(),
                            Select::make('ageGroups')
                                ->label('المجموعات العمرية المناسبة')
                                ->multiple()
                                ->relationship('ageGroups', 'name')
                                ->preload(),
                            Select::make('skills')
                                ->label('المهارات المستهدفة')
                                ->multiple()
                                ->relationship('skills', 'name')
                                ->preload(),
                            Select::make('needs')
                                ->label('الاحتياجات / الصعوبات')
                                ->multiple()
                                ->relationship('needs', 'name')
                                ->preload(),
                        ]),
                    ]),

                Section::make('الملفات والصور المعروضة')
                    ->schema([
                        FileUpload::make('images')
                            ->label('معرض صور المنتج')
                            ->multiple()
                            ->image()
                            ->directory('products')
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            FileUpload::make('digital_file_path')
                                ->label('الملف الرقمي للشيت (PDF)')
                                ->directory('private_downloads')
                                ->visibility('private')
                                ->acceptedFileTypes(['application/pdf']),
                            TextInput::make('digital_file_name')
                                ->label('اسم الملف عند التنزيل')
                                ->placeholder('arabic_letters.pdf'),
                            TextInput::make('digital_download_limit')
                                ->label('أقصى عدد مرات تنزيل')
                                ->numeric()
                                ->default(5),
                            TextInput::make('digital_expiry_days')
                                ->label('صلاحية رابط التنزيل (أيام)')
                                ->numeric()
                                ->default(30),
                        ]),
                    ]),

                Section::make('التفاصيل الإضافية وطريقة الاستخدام')
                    ->schema([
                        TextInput::make('video_url')
                            ->label('رابط فيديو توضيحي (YouTube)')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...'),
                        Grid::make(2)->schema([
                            TagsInput::make('benefits')
                                ->label('الفوائد المكتسبة (اضغط Enter بعد كل فائدة)')
                                ->placeholder('مثال: تنمية التركيز البصري'),
                            TagsInput::make('how_to_use')
                                ->label('طريقة الاستخدام (خطوات)')
                                ->placeholder('مثال: اعرض الكرت واطلب منه النطق'),
                        ]),
                        Textarea::make('whats_included')
                            ->label('محتويات العبوة أو الملف بالتفصيل')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('suitable_for')
                            ->label('الفئة المستهدفة بالتفصيل')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('مكونات الباقة (للباقات فقط)')
                    ->description('حدد المنتجات المندرجة داخل هذه الباقة')
                    ->schema([
                        Select::make('products')
                            ->label('المنتجات والأدوات المرفقة')
                            ->multiple()
                            ->relationship('products', 'name')
                            ->preload()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
