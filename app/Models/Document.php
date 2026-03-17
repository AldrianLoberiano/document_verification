<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'public_id',
        'verification_code',
        'title',
        'document_type',
        'document_number',
        'recipient_name',
        'issued_at',
        'expires_at',
        'status',
        'notes',
        'file_original_name',
        'file_mime_type',
        'file_size',
        'file_path',
        'file_checksum',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verificationAttempts(): HasMany
    {
        return $this->hasMany(VerificationAttempt::class);
    }
}
