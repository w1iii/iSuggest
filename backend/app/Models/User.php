<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['profile_image_url'];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'image',
        'bio',
        'title',
        'field',
        'password',
        'role',
        'remember_token',
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
