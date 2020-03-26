<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unidades;
use App\Citologia;
use App\Deteccion;
use App\Mastografia;
use App\Vph;

class PruebasController extends Controller
{
    public function testOrm(){

        $unidades = unidades::all(); //obtenemos todos los registros de la tabla unidaddes
        //var_dump($unidades);
        foreach($unidades as $unidad){
            echo  "<h1>".$unidad->nombre_unidad."</h1>";
            echo  "<h2>".$unidad->municipio."</h2>";
            echo  "<h3>".$unidad->clues."</h3>";
            echo  "<p>".$unidad->nom_responsable."</p>";
            echo  "<p>".$unidad->email."</p>";
        }

        die();
    }

    public function testOrmCitos()
    {
        $citologias = Citologia::all();

        foreach($citologias as $citologia)
        {
            echo "<h1>" .$citologia->meta_cito. "</h1>";
            echo "<span> {$citologia->unidades->nombre_unidad} </span>";
            echo "<hr>";
        }
    }

    
}