<?php

namespace App\Filament\Resources\Laporans\Tables;

use App\Models\Laporan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LaporansTable
{
    public static function configure(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $table
            ->query(function () use ($user) {
                $query = Laporan::query();

                if ($user->hasRole('mahasiswa')) {
                    // 👨‍🎓 hanya laporan miliknya
                    $query->where('mahasiswa_id', $user->id);
                }

                if ($user->hasRole('dosen')) {
                    // 👨‍🏫 laporan yang dibimbing langsung + mahasiswa bimbingannya
                    $query->where('dosen_id', $user->id)
                        ->orWhereHas('mahasiswa', function ($q) use ($user) {
                            $q->where('dosen_pembimbing_id', $user->id);
                        });
                }

                // 🧑‍💼 super_admin lihat semua laporan (tanpa filter)

                return $query;
            })
            ->recordUrl(null)
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->judul)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mahasiswa.name')
                    ->label('Mahasiswa')
                    ->searchable(),

                TextColumn::make('dosen.name')
                    ->label('Dosen Pembimbing')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ]),

                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date(),

                TextColumn::make('tanggal_berakhir')
                    ->label('Berakhir')
                    ->date(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Laporan Baru')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => $user->hasRole('mahasiswa')), 
            ])
            ->recordActions([
                EditAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => $user->hasRole('super_admin')),
                ]),
            ]);
    }
}
