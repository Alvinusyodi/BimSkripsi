<?php

namespace App\Filament\Resources\Bimbingans\Pages;

use App\Filament\Resources\Bimbingans\BimbinganResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBimbingan extends CreateRecord
{
    protected static string $resource = BimbinganResource::class;

    protected static ?string $title = 'Buat Bimbingan';

    protected static ?string $breadcrumb = 'Buat';
}
