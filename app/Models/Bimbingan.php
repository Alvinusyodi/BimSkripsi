<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Bimbingan extends Model
{
    protected $fillable = [
        'topik',
        'status',
        'status_domen',
        'user_id',
        'dosen_id',
        'tanggal',
        'isi',
        'type',
        'komentar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bimbingan) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if ($user && $user->hasRole('mahasiswa')) {
                $bimbingan->user_id = $user->id;
                $bimbingan->dosen_id = $user->dosen_pembimbing_id;
            }

            // Auto hitung pertemuan ke berapa
            if ($bimbingan->user_id) {
                $bimbingan->pertemuan_ke = static::where('user_id', $bimbingan->user_id)
                    ->count() + 1;
            }
        });
    }

    // Relationship: Bimbingan belongs to Mahasiswa
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Bimbingan belongs to Dosen
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // Scope untuk bimbingan mahasiswa tertentu
    public function scopeByMahasiswa($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk bimbingan dosen tertentu
    public function scopeByDosen($query, $dosenId)
    {
        return $query->where(function ($q) use ($dosenId) {
            $q->where('dosen_id', $dosenId)
                ->orWhereHas('mahasiswa', function ($subQuery) use ($dosenId) {
                    $subQuery->where('dosen_pembimbing_id', $dosenId);
                });
        });
    }

    // Scope untuk bimbingan by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk bimbingan by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function pertemuans()
    {
        return $this->hasMany(Pertemuan::class);
    }

}
