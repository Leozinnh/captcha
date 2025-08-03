<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CaptchaToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'domain',
        'allowed_types',
        'daily_limit',
        'usage_count',
        'last_used_at',
        'is_active',
        'description'
    ];

    protected $casts = [
        'allowed_types' => 'array',
        'last_used_at' => 'date',
        'is_active' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($token) {
            if (empty($token->token)) {
                $token->token = static::generateUniqueToken();
            }
        });
    }

    public static function generateUniqueToken()
    {
        do {
            $token = 'lc_' . Str::random(32);
        } while (static::where('token', $token)->exists());
        
        return $token;
    }

    public function incrementUsage()
    {
        // Reset contador se for um novo dia
        if ($this->last_used_at && $this->last_used_at->isToday()) {
            $this->increment('usage_count');
        } else {
            $this->update([
                'usage_count' => 1,
                'last_used_at' => now()
            ]);
        }
    }

    public function hasReachedDailyLimit()
    {
        if (!$this->last_used_at || !$this->last_used_at->isToday()) {
            return false;
        }
        
        return $this->usage_count >= $this->daily_limit;
    }

    public function canUseCaptchaType($type)
    {
        if (!$this->allowed_types) {
            return true; // Se não especificado, permite todos
        }
        
        return in_array($type, $this->allowed_types);
    }

    public static function getAvailableTypes()
    {
        return [
            'text' => 'Captcha de Texto',
            'robot' => 'Captcha Robot',
            'math' => 'Captcha Matemático',
            'grid' => 'Captcha de Grade',
            'dragdrop' => 'Captcha Drag & Drop',
            'voice' => 'Captcha de Voz'
        ];
    }
}