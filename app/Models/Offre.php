<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    /** @use HasFactory<\Database\Factories\OffreFactory> */
    use HasFactory;

    protected $fillable=['title','content','lieu','email','recruter_id'];

    public function users(){
        return $this->belongsToMany(User::class,'user_offre');
    }

    public function recruter(){
        return $this->belongsTo(User::class,'recruter_id');
    }
}
