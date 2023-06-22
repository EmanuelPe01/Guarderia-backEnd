<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'gratification',
        'id_child',
        'id_food',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class, 'id_food');
    }

    public function child()
    {
        return $this->belongsTo(Child::class, 'id_child');
    }
}
