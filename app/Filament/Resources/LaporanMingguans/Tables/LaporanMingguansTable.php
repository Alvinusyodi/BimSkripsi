<?php

namespace App\Filament\Resources\LaporanMingguans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanMingguan;
use Filament\Forms\Components\Select;

class LaporanMingguansTable
{
    public static function configure(Table $table): Table
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $table
            ->recordUrl(null)
            ->groups([
                Group::make('laporan.mahasiswa.name')
                    ->label('Mahasiswa')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(function ($record) {
                        // Hitung jumlah minggu completed
                        $totalMinggu = LaporanMingguan::whereHas('laporan', function ($q) use ($record) {
                            $q->where('mahasiswa_id', $record->laporan->user_id);
                        })
                        ->where('status', 'completed')
                        ->count();

                        return $record->laporan->mahasiswa->name . " ({$totalMinggu} minggu)";
                    }),
            ])
            ->defaultGroup('laporan.mahasiswa.name')
            ->groupsOnly(false)
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                TextColumn::make('laporan.judul')
                    ->label('Laporan Utama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('week')
                    ->label('Minggu Ke')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Hitung urut minggu hanya dari yang status completed
                        return LaporanMingguan::whereHas('laporan', function ($q) use ($record) {
                            $q->where('mahasiswa_id', $record->laporan->mahasiswa_id);
                        })
                        ->where('status', 'completed')
                        ->orderBy('created_at')
                        ->pluck('id')
                        ->search($record->id) + 1;
                    }),

                TextColumn::make('isi')
                    ->label('Isi / Link')
                    ->formatStateUsing(function ($state) {
                        if (filter_var($state, FILTER_VALIDATE_URL)) {
                            return "<a href='{$state}' target='_blank' class='text-primary-600 underline'>Buka Dokumen</a>";
                        }
                        return e(\Illuminate\Support\Str::limit($state, 80));
                    })
                    ->html()
                    ->searchable()
                    ->wrap(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'disetujui',
                        'danger' => 'revisi',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'revisi' => 'Revisi',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'revisi' => 'Revisi',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Laporan Mingguan')
                    ->icon('heroicon-o-plus')
                    ->visible(fn() => $user->hasRole('mahasiswa')),
            ])
            ->recordActions([
                Action::make('update_status')
                    ->label('Update Status')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'disetujui' => 'Disetujui',
                                'revisi' => 'Revisi',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update($data);
                    })
                    ->visible(fn($record) => $user->hasRole('dosen'))
                    ->modalHeading(fn($record) => "Update Status: {$record->laporan->mahasiswa->name}")
                    ->modalSubmitActionLabel('Simpan'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            // ->defaultSort('created_at', 'desc')
            ->deferLoading();
    }
}
