<?php

namespace App\Filament\Resources\LaporanMingguans\Pages;

use App\Filament\Resources\LaporanMingguans\LaporanMingguanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanMingguan extends CreateRecord
{
    protected static string $resource = LaporanMingguanResource::class;

    protected static ?string $title = 'Buat Laporan Mingguan';

    protected static ?string $breadcrumb = 'Buat';

}
