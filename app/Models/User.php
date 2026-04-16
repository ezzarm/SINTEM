<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    public $timestamps = true;

    protected $fillable = [
        'identifier',
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role_id'    => 'integer',
        'status'     => 'string',
        'last_login' => 'datetime',
    ];

    // Custom auth identifier (NIS-based login)
    public function getAuthIdentifierName(): string
    {
        return 'identifier';
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuperadmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isAdmin(): bool
    {
        return $this->role_id !== 1 && $this->role_id !== 2;
    }
}