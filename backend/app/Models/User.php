<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'image',
        'bio',
        'title',
        'field',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    protected function profileImageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return null;
            }
            return asset(Storage::url($this->image));
        });
    }
}
