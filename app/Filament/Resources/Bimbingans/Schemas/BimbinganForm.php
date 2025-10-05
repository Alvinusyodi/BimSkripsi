<?php

namespace App\Filament\Resources\Bimbingans\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class BimbinganForm
{
    public static function configure(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $schema
            ->columns(1)
            ->components([
                // ==================== SECTION INFORMASI BIMBINGAN ====================
                Section::make('Informasi Bimbingan')
                    ->schema([
                        // // FIELD MAHASISWA
                        // Select::make('user_id')
                        //     ->label('Mahasiswa')
                        //     ->options(function () use ($user) {
                        //         $query = User::query();

                        //         if ($user->hasRole('mahasiswa')) {
                        //             $query->where('id', $user->id);
                        //         } elseif ($user->hasRole('dosen')) {
                        //             $query->where('dosen_pembimbing_id', $user->id);
                        //         } elseif ($user->hasRole('super_admin')) {
                        //             $query->whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'));
                        //         }

                        //         return $query->pluck('name', 'id');
                        //     })
                        //     ->searchable()
                        //     ->preload()
                        //     ->required()
                        //     ->default(
                        //         $user->hasRole('mahasiswa')
                        //             ? $user->id
                        //             : null
                        //     )
                        //     ->disabled(
                        //         fn() => $user->hasRole('mahasiswa')
                        //     )
                        //     ->visible(
                        //         fn() => in_array($user->getRoleNames()->first(), ['super_admin', 'dosen', 'mahasiswa'])
                        //     )
                        //     ->columnSpan(1),

                        // // FIELD DOSEN PEMBIMBING
                        // Select::make('dosen_id')
                        //     ->label('Dosen Pembimbing')
                        //     ->options(function () use ($user) {
                        //         $query = User::query();

                        //         if ($user->hasRole('mahasiswa')) {
                        //             $query->where('id', $user->dosen_pembimbing_id);
                        //         } elseif ($user->hasRole('dosen')) {
                        //             $query->where('id', $user->id);
                        //         } elseif ($user->hasRole('super_admin')) {
                        //             $query->whereHas('roles', fn($q) => $q->where('name', 'dosen'));
                        //         }

                        //         return $query->pluck('name', 'id');
                        //     })
                        //     ->searchable()
                        //     ->preload()
                        //     ->nullable()
                        //     ->default(
                        //         $user->hasRole('mahasiswa')
                        //             ? $user->dosen_pembimbing_id
                        //             : ($user->hasRole('dosen') ? $user->id : null)
                        //     )
                        //     ->disabled(
                        //         fn() => $user->hasRole('mahasiswa') || $user->hasRole('dosen')
                        //     )
                        //     ->visible(
                        //         fn() => in_array($user->getRoleNames()->first(), ['super_admin', 'dosen', 'mahasiswa'])
                        //     )
                        //     ->columnSpan(1),

                        // FIELD MAHASISWA
                        Select::make('user_id')
                            ->label('Mahasiswa')
                            ->options(function () use ($user) {
                                $query = User::query();

                                if ($user->hasRole('mahasiswa')) {
                                    $query->where('id', $user->id);
                                } elseif ($user->hasRole('dosen')) {
                                    $query->where('dosen_pembimbing_id', $user->id);
                                } elseif ($user->hasRole('super_admin')) {
                                    $query->whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'));
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(
                                $user->hasRole('mahasiswa')
                                    ? $user->id
                                    : null
                            )
                            ->disabled(
                                fn() => $user->hasRole('mahasiswa')
                            )
                            ->visible(
                                fn() => in_array($user->getRoleNames()->first(), ['super_admin', 'dosen', 'mahasiswa'])
                            )
                            ->live() // Tambahkan live
                            ->afterStateUpdated(function ($state, $set) use ($user) {
                                // Hanya untuk super_admin, otomatis isi dosen pembimbing
                                if ($user->hasRole('super_admin') && $state) {
                                    $mahasiswa = User::find($state);
                                    if ($mahasiswa && $mahasiswa->dosen_pembimbing_id) {
                                        $set('dosen_id', $mahasiswa->dosen_pembimbing_id);
                                    }
                                }
                            })
                            ->columnSpan(1),

                        // FIELD DOSEN PEMBIMBING
                        Select::make('dosen_id')
                            ->label('Dosen Pembimbing')
                            ->options(function () use ($user) {
                                $query = User::query();

                                if ($user->hasRole('mahasiswa')) {
                                    $query->where('id', $user->dosen_pembimbing_id);
                                } elseif ($user->hasRole('dosen')) {
                                    $query->where('id', $user->id);
                                } elseif ($user->hasRole('super_admin')) {
                                    $query->whereHas('roles', fn($q) => $q->where('name', 'dosen'));
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->default(
                                $user->hasRole('mahasiswa')
                                    ? $user->dosen_pembimbing_id
                                    : ($user->hasRole('dosen') ? $user->id : null)
                            )
                            ->disabled(
                                fn() => $user->hasRole('mahasiswa') || $user->hasRole('dosen')
                            )
                            ->visible(
                                fn() => in_array($user->getRoleNames()->first(), ['super_admin', 'dosen', 'mahasiswa'])
                            )
                            ->columnSpan(1),

                        // TOPIK BIMBINGAN
                        TextInput::make('topik')
                            ->label('Topik Bimbingan')
                            ->maxLength(50)
                            ->required()
                            ->placeholder('Topik bimbingan')
                            ->columnSpanFull(),

                        // JENIS BIMBINGAN
                        Select::make('type')
                            ->label('Jenis Bimbingan')
                            ->options([
                                'proposal' => 'Proposal',
                                'skripsi' => 'Skripsi',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->default('proposal')
                            ->placeholder('Jenis bimbingan')
                            ->columnSpan(1),

                        // TANGGAL BIMBINGAN
                        DatePicker::make('tanggal')
                            ->label('Tanggal Bimbingan')
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->default(now())
                            ->placeholder('Tanggal bimbingan')
                            ->columnSpan(1),

                        // ISI BIMBINGAN
                        Textarea::make('isi')
                            ->label('Isi/Rincian Bimbingan')
                            ->maxLength(255)
                            ->rows(3)
                            ->required()
                            ->placeholder('Isi/rincian bimbingan')
                            ->columnSpanFull(),

                        // STATUS BIMBINGAN - UNTUK DOSEN/ADMIN (Select)
                        Select::make('status')
                            ->label('Status Bimbingan')
                            ->options([
                                'pending' => 'Pending - Menunggu persetujuan',
                                'approved' => 'Approved - Disetujui',
                                'rejected' => 'Rejected - Ditolak',
                                'completed' => 'Completed - Selesai',
                            ])
                            ->default('pending')
                            ->required()
                            ->visible($user->hasRole('super_admin') || $user->hasRole('dosen'))
                            ->columnSpan(1),

                        // STATUS BIMBINGAN - HIDDEN UNTUK MAHASISWA
                        Hidden::make('status')
                            ->default('pending')
                            ->visible($user->hasRole('mahasiswa')),

                        // STATUS PROSES - UNTUK DOSEN/ADMIN (Select)
                        Select::make('status_domen')
                            ->label('Status Proses')
                            ->options([
                                'revisi' => '🔄 Butuh Revisi',
                                'review' => '👀 Dalam Review',
                                'fix' => '✅ Sudah Fix',
                                'acc' => '🎉 Diterima (ACC)',
                                'tolak' => '❌ Ditolak',
                                'selesai' => '🏁 Selesai',
                            ])
                            ->default('review')
                            ->placeholder('Pilih status proses bimbingan')
                            ->visible($user->hasRole('super_admin') || $user->hasRole('dosen'))
                            ->columnSpan(1),

                        // STATUS PROSES - UNTUK MAHASISWA (TextInput readonly)
                        TextInput::make('status_domen')
                            ->label('Status Proses')
                            ->formatStateUsing(fn($state) => match ($state) {
                                'revisi' => '🔄 Butuh Revisi',
                                'review' => '👀 Dalam Review',
                                'fix' => '✅ Sudah Fix',
                                'acc' => '🎉 Diterima (ACC)',
                                'tolak' => '❌ Ditolak',
                                'selesai' => '🏁 Selesai',
                                default => 'Belum ada status',
                            })
                            ->disabled()
                            ->visible(
                                fn($get) => $user->hasRole('mahasiswa') && !empty($get('status_domen'))
                            )
                            ->columnSpan(1),

                        // KOMENTAR - UNTUK DOSEN/ADMIN (Textarea enabled)
                        Textarea::make('komentar')
                            ->label('Komentar untuk Mahasiswa')
                            ->maxLength(100)
                            ->rows(2)
                            ->placeholder('Berikan komentar dan saran untuk mahasiswa')
                            ->visible($user->hasRole('super_admin') || $user->hasRole('dosen'))
                            ->columnSpanFull(),

                        // KOMENTAR - UNTUK MAHASISWA (Textarea readonly)
                        Textarea::make('komentar')
                            ->label('Komentar Dosen')
                            ->maxLength(100)
                            ->rows(2)
                            ->placeholder('Belum ada komentar dari dosen')
                            ->disabled()
                            ->visible(
                                fn($get) => $user->hasRole('mahasiswa') && !empty($get('komentar'))
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
