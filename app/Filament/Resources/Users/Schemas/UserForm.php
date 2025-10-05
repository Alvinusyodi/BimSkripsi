<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Nama lengkap'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Email address'),

                TextInput::make('password')
                    ->password()
                    ->required(fn ($operation) => $operation === 'create')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->placeholder('Password minimal 8 karakter')
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn ($operation) => $operation === 'create')
                    ->dehydrated(false)
                    ->placeholder('Konfirmasi password'),

                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive', 
                        'suspended' => 'Suspended',
                    ])
                    ->required()
                    ->default('active'),

                TextInput::make('angkatan')
                    ->maxLength(10)
                    ->placeholder('Contoh: 2024')
                    ->nullable(),

                Select::make('dosen_pembimbing_id')
                    ->label('Dosen Pembimbing')
                    ->relationship(
                        name: 'dosenPembimbing', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereHas('roles', function ($q) {
                            $q->where('name', 'dosen');
                        })
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder('Pilih dosen pembimbing'),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih role user'),
            ]);
    }
}
