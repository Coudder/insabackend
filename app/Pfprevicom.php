<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pfprevicom extends Model
{
    //modelo de la tabla prev_comunidad prevencion violencia comunidad
    protected $fillable= ['unidad_id','responsable_id','coordinador_id','jurisdiccion_id','meta_comunidad', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_comunidad','ano'];

    protected $table = 'pf_prev_comunidad';


    public function unidades(){
        return $this->belongsTo('App\Unidades', 'unidad_id');
    }

    public function responsables(){
        return $this->belongsTo('App\Responsables', 'responsable_id');
    }

    public function coordinadores(){
        return $this->belongsTo('App\Coordinadores', 'coordinador_id');
    }

    public function jurisdiccion(){
        return $this->belongsTo('App\Jurisdiccion', 'jurisdiccion_id');
    }
}
