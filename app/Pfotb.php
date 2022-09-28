<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pfotb extends Model
{
    //modelo de la tabla pf_otb
    protected $fillable= ['unidad_id','responsable_id','coordinador_id','jurisdiccion_id','meta_otb', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_otb','ano'];

    protected $table = 'pf_otb';


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
