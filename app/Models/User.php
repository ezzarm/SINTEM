<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    // Tell Laravel this table has no created_at / updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'identifier',
        'name',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'identifier';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}