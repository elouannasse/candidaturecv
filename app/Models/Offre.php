<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    /** @use HasFactory<\Database\Factories\OffreFactory> */
    use HasFactory;

    protected $fillable=['title','content','lieu'];

    public function users(){
        return $this->belongsToMany(User::class,'user_offre');
    }
}
