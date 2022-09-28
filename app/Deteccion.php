<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deteccion extends Model
{
    protected $fillable= ['unidad_id','meta_cito', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_det','ano'];
    protected $table = 'detecciones';
    //
    public function unidades(){
        return $this->belongsTo('App\Unidades', 'unidad_id');
    }
    //
}
