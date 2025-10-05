<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class SampleUsersSeeder extends Seeder
{
    public function run()
    {
        // Dosen
        $dosen = User::create([
            'name' => 'Alvinus Yodi',
            'email' => 'dosen@example.com',
            'password' => Hash::make('dosen123'),
        ]);
        $dosen->assignRole('dosen');

        // Mahasiswa
        $mahasiswa = User::create([
            'name' => 'Audi Natanel', 
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('mahasiswa123'),
        ]);
        $mahasiswa->assignRole('mahasiswa');

        $this->command->info('Sample users created!');
    }
}