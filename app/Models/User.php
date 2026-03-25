<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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
        'role_id' => 'integer',
        'status'  => 'string',
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