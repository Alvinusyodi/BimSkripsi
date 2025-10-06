<x-filament-widgets::widget>
    <x-filament::grid>
        <x-filament::card>
            <h3 class="text-lg font-bold">Mahasiswa</h3>
            <p class="text-2xl">{{ $stats['mahasiswa_count'] }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold">Dosen</h3>
            <p class="text-2xl">{{ $stats['dosen_count'] }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold">Bimbingan</h3>
            <p class="text-2xl">{{ $stats['bimbingan_count'] }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold">Laporan</h3>
            <p class="text-2xl">{{ $stats['laporan_count'] }}</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold">Laporan Mingguan</h3>
            <p class="text-2xl">{{ $stats['laporan_mingguan_count'] }}</p>
        </x-filament::card>
    </x-filament::grid>

    @if(isset($stats['bimbingan_mahasiswa']))
        <x-filament::card class="mt-4">
            <h3 class="text-lg font-bold mb-2">Bimbingan Saya</h3>
            <ul class="space-y-1">
                @foreach($stats['bimbingan_mahasiswa'] as $b)
                    <li>
                        <strong>{{ $b->topik }}</strong> - 
                        <span class="text-sm text-gray-500">{{ $b->status ?? $b->status_domen ?? 'Pending' }}</span>
                    </li>
                @endforeach
            </ul>
        </x-filament::card>
    @endif
</x-filament-widgets::widget>
