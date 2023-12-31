<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'importance',
        'title',
        'body',
        'id_group'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group');
    }
}
