<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Citologia extends Model
{
    protected $fillable= ['unidad_id','meta_cito', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'total_cito', 'ano'];
    protected $table = 'citologias';
    //
    public function unidades(){
        return $this->belongsTo('App\Unidades', 'unidad_id');
    }
}
