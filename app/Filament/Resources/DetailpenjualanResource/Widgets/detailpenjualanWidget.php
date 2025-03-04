<?php

namespace App\Filament\Resources\DetailpenjualanResource\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class detailpenjualanWidget extends BaseWidget
{
    public $penjualanId;
    public function mount($record){
        $this->penjualanId = $record;
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\detailpenjualan::query()->where('penjualan_id', $this->penjualanId)
            )
            ->columns([
                TextColumn::make('produk.nama_produk')
                ->label('Nama Produk'),
                TextColumn::make('jumlah_produk'),
                TextColumn::make('produk.harga')
                ->label('Harga')
                ->money('IDR'),
                TextColumn::make('subtotal')
                ->money('IDR'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
