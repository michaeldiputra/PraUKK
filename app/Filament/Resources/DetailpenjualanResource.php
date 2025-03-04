<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Detailpenjualan;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DetailpenjualanResource\Pages;
use App\Filament\Resources\DetailpenjualanResource\RelationManagers;

class DetailpenjualanResource extends Resource
{
    
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = Detailpenjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Detail Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('penjualan_id')
                    ->default(request('penjualan_id'))
                    ->dehydrated(),
                Select::make('produk_id')
                    ->options(
                        \App\Models\Produk::pluck('nama_produk', 'id')
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $produk = \App\Models\Produk::find($state);
                        $harga = $produk->harga ?? 0;
                        $jumlahProduk = $get('jumlah_produk') ?? 1;

                        $set('harga', $harga);
                        $set('jumlah_produk', 1);
                        $set('subtotal', $harga * $jumlahProduk);
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('jumlah_produk')
                    ->numeric()
                    ->default('1')
                    ->minValue('1')
                    ->maxValue(function ($get) {
                        $produkId = $get('produk_id');
                        $produk = \App\Models\Produk::find($produkId);
                        return $produk ? $produk->stok : 0;
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $harga = $get('harga');
                        $jumlahProduk = $get('jumlah_produk') ?? 1;

                        $set('subtotal', $harga * $jumlahProduk);
                    })
                    ->required(),
                TextInput::make('harga')
                    ->prefix('Rp')
                    ->disabled(),
                TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('produk.nama_produk'),
                TextColumn::make('jumlah_produk'),
                TextColumn::make('produk.harga'),
                TextColumn::make('subtotal'),
                TextColumn::make('penjualan_id'),
            ])
            ->filters([
                SelectFilter::make('penjualan_id')
                ->label('Pilih Penjualan')
                ->options(
                    \App\Models\Penjualan::with('pelanggan')
                        ->get()
                        ->pluck('pelanggan.nama_pelanggan', 'id')
                )
            ])
            ->actions([
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
            'index' => Pages\ListDetailpenjualans::route('/'),
            'create' => Pages\CreateDetailpenjualan::route('/create'),
            'edit' => Pages\EditDetailpenjualan::route('/{record}/edit'),
        ];
    }
}
