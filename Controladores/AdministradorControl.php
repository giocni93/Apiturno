<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Http\JsonResponse;

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
	        //$administrador->idempresa		=   $data['idempresa'];
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


  	function postadminempresa(Request $request, Response $response){
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);

	    try{
	        $administrador = new Administrador;
	        $administrador->nombres       	=   $data['nombres'];
	        $administrador->identificacion  =   $data['identificacion'];
	        $administrador->pass            =   sha1($data['pass']);
	        $administrador->estado          =   "INACTIVO";
	        $administrador->idperfil		=  	$data['idperfil'];
	        $administrador->correo 			= 	$data['correo'];
	        $administrador->idempresa		=   $data['idempresa'];
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

  	function postadminsucursal(Request $request, Response $response){
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	        $administrador = new Administrador;
	        $administrador->nombres       	=   $data['nombres'];
	        $administrador->apellidos		= 	$data['apellidos'];
	        $administrador->identificacion  = 	$data['identificacion'];
	        $administrador->pass            =   sha1($data['pass']);
	        $administrador->telefono		= 	$data['telefono'];
	        $administrador->estado          =   "ACTIVO";
	        $administrador->idperfil		=  	$data['idperfil'];
	        $administrador->idSucursal		= 	$data['idSucursal'];
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
	                    ->where("administrador.estado","=","ACTIVO")
	                    ->first();

	    $respuesta = json_encode(array('admin' => $data, "std" => 1));

	    if($data == null){
	      $respuesta = json_encode(array('admin' => null, "std" => 0));
	      $response = $response->withStatus(404);
	    }

	    $response->getBody()->write($respuesta);
	    return $response;
  	}

  	function updateadminestado(Request $request,Response $response){
        try{
        	$response = $response->withHeader('Content-type', 'application/json');
        	$data = json_decode($request->getBody(),true);
            $id = $request->getAttribute("id");
            $admin = Administrador::select("*")
                          ->where("idempresa","=",$id)
                          ->first();
            $admin->estado   =   $data['estado'];
            $admin->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function updateadminestadodesactivar(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $admin = Administrador::select("*")
                          ->where("idempresa","=",$id)
                          ->first();
            $admin->estado   =   $data['estado'];
            $admin->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function getIdAdmin(Request $request,Response $response){
    	$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Administrador::select("*")
	                    ->where("idSucursal","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
    }

    

}