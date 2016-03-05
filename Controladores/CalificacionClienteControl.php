<?php
use Slim\Http\Request;
use Slim\Http\Response;

class CalificacionClienteControl{

    function post(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    try{
        $calificacion = new CalificacionCliente;
        $calificacion->idCliente        =   $data['idCliente'];
        $calificacion->idEmpleado       =   $data['idEmpleado'];
        $calificacion->calificacion     =   $data['calificacion'];
        $calificacion->save();
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
