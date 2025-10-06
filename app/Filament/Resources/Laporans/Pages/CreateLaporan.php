<?php

namespace App\Filament\Resources\Laporans\Pages;

use App\Filament\Resources\Laporans\LaporanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporan extends CreateRecord
{
    protected static string $resource = LaporanResource::class;

    protected static ?string $title = 'Buat Laporan';

    protected static ?string $breadcrumb = 'Buat';
}
