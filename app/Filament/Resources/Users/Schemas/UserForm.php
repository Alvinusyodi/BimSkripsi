<?php

namespace App\Filament\Resources\Users\Schemas;

use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // 🧩 1. Role duluan
            Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable()
                ->required()
                ->reactive()
                ->placeholder('Pilih role user'),


            TextInput::make('npm')
                ->label('NPM')
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->placeholder('Masukkan NPM mahasiswa')
                ->visible(function ($get) {
                    $roles = Role::whereIn('id', (array) $get('roles'))->pluck('name')->toArray();
                    return in_array('mahasiswa', $roles);
                })
                ->nullable(),

            TextInput::make('nidn')
                ->label('NIDN')
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->placeholder('Masukkan NIDN dosen')
                ->visible(function ($get) {
                    $roles = Role::whereIn('id', (array) $get('roles'))->pluck('name')->toArray();
                    return in_array('dosen', $roles);
                })
                ->nullable(),


            // 🧍 Nama
            TextInput::make('name')
                ->required()
                ->maxLength(100)
                ->placeholder('Nama lengkap'),

            // 📧 Email
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true)
                ->placeholder('Email address'),

            // 🔒 Password
            TextInput::make('password')
                ->password()
                ->required(fn($operation) => $operation === 'create')
                ->dehydrateStateUsing(fn($state) => Hash::make($state))
                ->dehydrated(fn($state) => filled($state))
                ->minLength(8)
                ->maxLength(255)
                ->placeholder('Password minimal 8 karakter')
                ->confirmed(),

            // 🔒 Konfirmasi Password
            TextInput::make('password_confirmation')
                ->password()
                ->required(fn($operation) => $operation === 'create')
                ->dehydrated(false)
                ->placeholder('Konfirmasi password'),

            // ⚙️ Status
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'suspended' => 'Suspended',
                ])
                ->required()
                ->default('active'),

            // 🎓 Angkatan
            TextInput::make('angkatan')
                ->maxLength(10)
                ->placeholder('Contoh: 2024')
                ->visible(function ($get) {
                    $roles = Role::whereIn('id', (array) $get('roles'))->pluck('name')->toArray();
                    return in_array('mahasiswa', $roles);
                })
                ->nullable(),

            Select::make('dosen_pembimbing_id')
                ->label('Dosen Pembimbing')
                ->relationship(
                    name: 'dosenPembimbing',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn($query) => $query->whereHas('roles', function ($q) {
                        $q->where('name', 'dosen');
                    })
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive()
                ->hidden(function ($get) {
                    $roles = \Spatie\Permission\Models\Role::whereIn('id', (array) $get('roles'))
                        ->pluck('name')
                        ->toArray();
                    return !in_array('mahasiswa', $roles);
                })
                ->placeholder('Pilih dosen pembimbing'),
        ]);
    }
}
