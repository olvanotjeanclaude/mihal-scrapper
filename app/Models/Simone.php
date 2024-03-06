<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simone extends Model
{
    use HasFactory;

    protected $fillable = ["data"];

    public function getDataAttribute($json){
        return json_decode($json);
    }
}
