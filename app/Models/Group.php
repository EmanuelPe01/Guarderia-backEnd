<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'name'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id_user');
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'id_group');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'id_group');
    }
}
