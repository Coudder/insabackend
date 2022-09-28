<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pfvsb extends Model
{
    //modelo de la tabla pf_vsb
    protected $fillable= ['unidad_id','responsable_id','coordinador_id','jurisdiccion_id','meta_vsb', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_vsb','ano'];

    protected $table = 'pf_vsb';


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
 