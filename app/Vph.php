<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vph extends Model
{
    protected $fillable= ['unidad_id','meta_vph', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_vphs', 'ano'];
    protected $table = 'vphs';
    //
    public function unidades(){
        return $this->belongsTo('App\Unidades', 'unidad_id');
    }
}
