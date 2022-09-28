<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Jurisdiccion;

class JurController extends Controller
{
    public function __construct()
    {
        //aqui le decimos que use el middlewarte de auth en todos los metodos except los que nosotros no queramos
        //en este caso en el metodo index y show
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $jurisdiccion = Jurisdiccion::all();

        if(!empty($jurisdiccion))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'jurisdiccion' => $jurisdiccion
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe información disponible'
            ];    
        }
        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        $jefeJur = Jurisdiccion::find($id);

        if(is_object($jefeJur))
        {
            $data = [
                'code' => 200,
                'status' => 'success',
                'jurisdiccion' => $jefeJur
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No existe Registro de Responsable'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        $json = $request -> input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if(!empty($params_array))
        {
            $validate = \Validator::make($params_array,[
                'name' => 'required',
                'email' => 'required|email|unique:jurisdiccional',
                'password' => 'required',
                'jurisdiccion' => 'required|unique:jurisdiccional'
            ]);

            if($validate->fails())
            {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El registro no se ah guardado correctamenta',
                    'errors' => $validate->errors()
                    
                ];
            }else{
                $pwd = hash('sha256', $params->password);

                $jurisdiccion = new Jurisdiccion();
                $jurisdiccion->name = $params_array['name'];
                $jurisdiccion->email = $params_array['email'];
                $jurisdiccion->password = $pwd;
                $jurisdiccion->jurisdiccion = $params_array['jurisdiccion'];

                $jurisdiccion->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'el registro se ah creado correctamente',
                    'jurisdiccion' => $jurisdiccion
                ];
            }
        }else{
            $data =[
                'code' => 400,
                'status' => 'error',
                'message' => 'El registro no se ah realizado correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if(!empty($params_array))
        {
            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'email' => 'required|email|unique:jurisdiccional',
                'password' => 'required',
                'jurisdiccion' => 'required|unique:jurisdiccional'
            ]);

            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['password']);

            //actualizamos

            $jurisdiccion = Jurisdiccion::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'jurisdiccion' => $params_array
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah actualizado el registro correctamente'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request)
    {
        $jurisdiccion_delete = Jurisdiccion::find($id);

        if(!empty($jurisdiccion_delete))
        {
            $jurisdiccion_delete->delete();

            $data =[
                'code' => 200,
                'status' => 'success',
                'message' => 'Se ah eliminado el registro correctamente',
                'jurisdiccion' => $jurisdiccion_delete
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ah eliminado el registro',
                'jurisdiccion' => $jurisdiccion_delete

            ];
        }
        return response()->json($data, $data['code']);
    }
}
