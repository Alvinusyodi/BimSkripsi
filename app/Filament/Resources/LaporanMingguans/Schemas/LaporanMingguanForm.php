<?php

namespace App\Filament\Resources\LaporanMingguans\Schemas;

use App\Models\Laporan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class LaporanMingguanForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 🔍 Ambil query laporan sesuai role
        $laporanQuery = Laporan::query();

        // Jika mahasiswa, hanya ambil laporan miliknya
        if ($user->hasRole('mahasiswa')) {
            $laporanQuery->where('mahasiswa_id', $user->id);
        }

        // 🧱 Komponen form
        $components = [
            Select::make('laporan_id')
                ->label('Laporan Utama')
                ->options($laporanQuery->pluck('judul', 'id'))
                ->searchable()
                ->required()
                ->placeholder('Pilih laporan utama...'),

            TextInput::make('week')
                ->label('Minggu Ke-')
                ->numeric()
                ->minValue(1)
                ->required(),

            TextInput::make('isi')
                ->label('Link Dokumen Laporan')
                ->placeholder('Tempel link Google Docs / Drive di sini...')
                ->url() // validasi otomatis untuk format URL
                ->required()
                ->suffixIcon('heroicon-o-link')
                ->helperText('Masukkan link dokumen laporan mingguan (contoh: https://docs.google.com/...).'),
        ];

        // Jika user dosen atau super admin → tambahkan kolom status
        if ($user->hasAnyRole(['dosen', 'super admin'])) {
            $components[] = Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'disetujui' => 'Disetujui',
                    'revisi' => 'Revisi',
                ])
                ->default('pending');
        }

        return $schema->components($components);
    }
}
