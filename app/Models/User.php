<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_verification_token_expires_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Vérifier si l'email est vérifié
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Marquer l'email comme vérifié
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_token_expires_at' => null,
        ])->save();
    }

    /**
     * Générer un token de vérification
     */
    public function generateEmailVerificationToken()
    {
        $token = bin2hex(random_bytes(32));

        $this->forceFill([
            'email_verification_token' => hash('sha256', $token),
            'email_verification_token_expires_at' => now()->addHours(24),
        ])->save();

        return $token;
    }

    public function freelance()
    {
        return $this->hasOne(Freelance::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function uploadedAttachments()
    {
        return $this->hasMany(Attachement::class, 'uploaded_by');
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isClient()
    {
        return $this->user_type === 'client';
    }

    public function isFreelance()
    {
        return $this->user_type === 'freelance';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
