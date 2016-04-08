<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class GaleriaControl {
    
    function addgaleria(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $galeria = new Galeria;
            $galeria->logo  =  $data['logo'];
            $galeria->idSucursal = $data['idsucursal'];
            $galeria->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }
    
}
