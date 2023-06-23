<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_surname',
        'second_surname',
        'id_group',
        'id_user',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    public function ingestion()
    {
        return $this->hasMany(Ingestion::class, 'id_child');
    }

}
