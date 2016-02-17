<?php
use Slim\Http\Request;
use Slim\Http\Response;

class SucursalControl{

	function postSucursal(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	        $sucursal = new Sucursal;
	        $sucursal->idEmpresa   =  $data['idEmpresa'];
	        $sucursal->idMunicipio =  $data['idMunicipio'];
	        $sucursal->nombre 	   =  $data['nombre'];
	        $sucursal->direccion   =  $data['direccion'];
	        $sucursal->telefono    =  $data['telefono'];
	        $sucursal->latitud     =  $data['latitud'];
	        $sucursal->longitud   =   $data['longitud'];
	        $sucursal->promedio    =  $data['promedio'];
	        $sucursal->estado      =  "ACTIVO";
	        $sucursal->save();
	        //$val = $servicio->id;
	        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
	        $response = $response->withStatus(200);
	    }catch(Exception $err){
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
	}
    
    function Versucursales(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
	    $data = Sucursal::select('*')->get();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
    }

}