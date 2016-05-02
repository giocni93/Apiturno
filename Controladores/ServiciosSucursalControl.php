<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class ServiciosSucursalControl{

	function postserviciossucursal(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	    	for($i=0; $i< count($data['servicios']);$i++){
	    		$servicio = new ServiciosSucursal;
	    		$servicio->idServicio =	 $data['servicios'][$i]['idservicio'];
	    		$servicio->idSucursal =  $data['servicios'][$i]['idsucursal'];
                        $servicio->precio = '0';
                        $servicio->precioVIP = '0';
	    		$servicio->save();
	    	}
	    	
	    	$respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
	        $response = $response->withStatus(200);
	    	
	    }catch(Exception $err){
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
	}

	function guardarserviciossucursal(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $servicio = new ServiciosSucursal;
            $servicio->idServicio   =  $data['idServicio'];
            $servicio->idSucursal 	=  $data['idSucursal'];
            $servicio->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
	}

	function getallid(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = ServiciosSucursal::select("serviciossucursal.*","servicio.nombre as servicio")
	    				->join("servicio","servicio.id","=","serviciossucursal.idServicio")
	                    ->where("idSucursal","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}

	function deleteserviciosucursal(Request $request, Response $response){
		try {
		    $response = $response->withHeader('Content-type', 'application/json');
		    $id = $request->getAttribute("id");
		    $servicio = ServiciosSucursal::select("*")
		                    ->where("idSucursal","=",$id)
		                    ->delete();
		    $respuesta = json_encode(array('msg' => "Eliminado correctamente", "std" => 1));
		    $response = $response->withStatus(200);
    	} catch (Exception $err) {
	    	$respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	    	$response = $response->withStatus(404);
    	}
    		$response->getBody()->write($respuesta);
    		return $response;
	}

	function putserviciosucursalprecios(Request $request, Response $response){
		try {
	        $response = $response->withHeader('Content-type', 'application/json');
		    $data = json_decode($request->getBody(),true);
		    $id = $request->getAttribute("id");
		    $servi = ServiciosSucursal::select("*")
                          ->where("id","=",$id)
                          ->first();
		      
		    $servi->precio            =   $data['precio'];
		    $servi->precioVIP         =   $data['precioVIP'];
		    $servi->save();

	      	$respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
	      	$response = $response->withStatus(200);
	    } catch (Exception $err) {
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    	return $response;
	}
        
        function agregartiempo(Request $request, Response $response){
            try {
                $response = $response->withHeader('Content-type', 'application/json');
                $data = json_decode($request->getBody(),true);
		    $id = $request->getAttribute("id");
		    $servi = ServiciosSucursal::select("*")
                          ->where("id","=",$id)
                          ->first();
                    $servi->minutos     =   $data['minutos'];
		    $servi->save();
                $respuesta = json_encode(array('msg' => "Tiempo modificado correctamente", "std" => 1));
	      	$response = $response->withStatus(200);
            } catch (Exception $err) {
                $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
            }
        }

        function getTiempo(Request $request, Response $response)
        {
			$response = $response->withHeader('Content-type', 'application/json');
            $idServicio = $request->getAttribute("idServicio");  
            $idSucursal = $request->getAttribute("idSucursal");
            $tiempo = ServiciosSucursal::select("minutos")
            							->where("idServicio", "=", $idServicio)
            							->where("idSucursal", "=", $idSucursal)
            							->first();
            $response->getBody()->write($tiempo);
            return $response;
        }

}
