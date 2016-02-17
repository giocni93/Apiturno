<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

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

		function getSucursalesByPosicion(Request $request, Response $response){
				$response = $response->withHeader('Content-type', 'application/json');
				$data = Parametros::select("*")->first();
				$km = $data["diametro_busqueda"];
				$idEmpresa = $request->getAttribute("idEmpresa");
				$lat = $request->getAttribute("latitud");
				$lng = $request->getAttribute("longitud");
				$query = "SELECT "
	                . "(6371 * ACOS( SIN(RADIANS(su.latitud)) * SIN(RADIANS($lat)) + COS(RADIANS(su.longitud - $lng)) * "
									. "COS(RADIANS(su.latitud)) * COS(RADIANS($lat)))) AS distancia, "
									. "su.* "
	                . "FROM sucursal su "
	                . "WHERE su.Estado = 'ACTIVO' AND su.idEmpresa = $idEmpresa "
									. "HAVING distancia < $km ORDER BY distancia ASC";
	      $data = DB::select(DB::raw($query));
		    $response->getBody()->write(json_encode($data));
		    return $response;
		}

}
