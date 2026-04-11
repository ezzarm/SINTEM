<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public $timestamps = true;

    protected $fillable = [
        'role_name',
        'description',
    ];

    // ── Users belonging to this role ──
    public function users()
    {
        return $this->hasMany(User::class);
    }
}