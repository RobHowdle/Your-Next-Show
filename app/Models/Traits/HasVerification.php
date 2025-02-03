<?php

namespace App\Models\Traits;

trait HasVerification
{
    public function verify()
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverify()
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }
}