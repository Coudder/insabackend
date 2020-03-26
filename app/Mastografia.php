<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mastografia extends Model
{
    protected $fillable= ['unidad_id','meta_masto', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_mastos', 'ano'];
    protected $table = 'mastografias';
    //
    public function unidades(){
        return $this->belongsTo('App\Unidades', 'unidad_id');
    }
    //
}
