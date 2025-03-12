<?php

namespace App\Repositories;

use App\Interfaces\OffreRepositoryInterface;
use App\Models\Offre;

class OffreRepository implements OffreRepositoryInterface
{
    /**
     * Create a new class instance.
     */

     public function index(){
        return Offre::all();
     }


     public function getById($id){
        return Offre::findOrFail($id);
     }
 
     public function store(array $data){
        return Offre::create($data);
     }
 
     public function update(array $data,$id){
        return Offre::whereId($id)->update($data);
     }
     
     public function delete($id){
        Offre::destroy($id);
     }
    
}
