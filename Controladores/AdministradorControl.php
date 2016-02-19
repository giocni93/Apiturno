<?php
use Slim\Http\Request;
use Slim\Http\Response;

class AdministradorControl{

	function post(Request $request, Response $response){
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);

	    try{
	        $administrador = new Administrador;
	        $administrador->nombres       	=   $data['nombres'];
	        $administrador->apellidos   	=   $data['apellidos'];
	        $administrador->identificacion  =   $data['identificacion'];
	        $administrador->pass            =   sha1($data['pass']);
	        $administrador->idperfil		=  	$data['idperfil'];
	        $administrador->correo 			= 	$data['correo'];
	        $administrador->estado          =   "ACTIVO";
	        $administrador->save();
	        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
	        $response = $response->withStatus(200);
	    }catch(Exception $err){
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
  	}

  	function sesion(Request $request, Response $response){
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    $data = Administrador::select("administrador.id","administrador.nombres","administrador.apellidos","administrador.identificacion","administrador.estado","perfil.nombre as nombreperfil","perfil.id as idperfil","administrador.correo","administrador.idempresa")
	    				->join("perfil","perfil.id","=","administrador.idperfil")
	    				->join("perfilpermisos","perfilpermisos.idperfil","=","perfil.id")
	                    ->where("pass","=",sha1($data['pass']))
	                    ->where("identificacion","=",$data['identificacion'])
	                    ->first();
	    $respuesta = json_encode(array('admin' => $data, "std" => 1));
	    if($data == null){
	      $respuesta = json_encode(array('admin' => null, "std" => 0));
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
  	}

  	

}