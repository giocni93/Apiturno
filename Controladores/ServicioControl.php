<?php
use Slim\Http\Request;
use Slim\Http\Response;

class ServicioControl{

	function postServicios(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	        $servicio = new Servicio;
	        $servicio->idEmpresa   =  $data['idEmpresa'];
	        $servicio->nombre 	   =  $data['nombre'];
	        $servicio->descripcion =  $data['descripcion'];
	        $servicio->estado      =  "ACTIVO";
	        $servicio->save();
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
	
	function getAllservicios(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Servicio::select('servicio.nombre','servicio.descripcion','servicio.estado','servicio.id')
	    	->where('servicio.idEmpresa','=',$id)
	    	->where('servicio.estado','=','ACTIVO')
	    	->get();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
  	}

	function getAll(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Servicio::select('empresa.razonSocial','empresa.nit','servicio.nombre','servicio.descripcion','servicio.estado','servicio.id')->join('empresa','empresa.id','=','servicio.idEmpresa')->get();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
  	}

  	function updateservicios(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $servicio = Servicio::find($id);
            $servicio->nombre   	 =   $data['nombre'];
            $servicio->descripcion   =   $data['descripcion'];
            $servicio->save();
            $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      		$response = $response->withStatus(200);
    		} catch (Exception $err) {
		      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
		      $response = $response->withStatus(404);
		    }
		    $response->getBody()->write($respuesta);
		    return $response;
  	}

  	function updateestado(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $servicio = Servicio::find($id);
            $servicio->estado   	 =   $data['estado'];
            $servicio->save();
            $respuesta = json_encode(array('msg' => "Activado correctamente", "std" => 1));
      		$response = $response->withStatus(200);
		} catch (Exception $err) {
	      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	      $response = $response->withStatus(404);
	    }
		    $response->getBody()->write($respuesta);
		    return $response;
  	}

  	function updateestadodesactivar(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $servicio = Servicio::find($id);
            $servicio->estado   	 =   $data['estado'];
            $servicio->save();
            $respuesta = json_encode(array('msg' => "Desactivado correctamente", "std" => 1));
      		$response = $response->withStatus(200);
		} catch (Exception $err) {
	      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	      $response = $response->withStatus(404);
	    }
		    $response->getBody()->write($respuesta);
		    return $response;
  	}

}
