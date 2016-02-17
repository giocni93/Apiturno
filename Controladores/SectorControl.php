<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class SectorControl{

	function getAll(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Sector::all();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;

	}

	function getSectorEmpresas(Request $request, Response $response){
			$response = $response->withHeader('Content-type', 'application/json');
			$data = Parametros::select("*")->first();
			$km = $data["diametro_busqueda"];
			$idSector = $request->getAttribute("id");
			$lat = $request->getAttribute("latitud");
			$lng = $request->getAttribute("longitud");
			$query = "SELECT "
                . "(6371 * ACOS( SIN(RADIANS(su.latitud)) * SIN(RADIANS($lat)) + COS(RADIANS(su.longitud - $lng)) * "
								. "COS(RADIANS(su.latitud)) * COS(RADIANS($lat)))) AS distancia, "
								. "em.id, "
								. "em.nit, "
								. "em.razonSocial, "
								. "em.logo, "
								. "'' as servicios "
                . "FROM sucursal su "
								. "INNER JOIN "
								. "empresa em ON (em.id = su.idEmpresa) "
								. "INNER JOIN "
								. "sectorempresa secemp ON (secemp.idEmpresa = em.id) "
                . "WHERE su.Estado = 'ACTIVO' AND em.estado = 'ACTIVO' AND secemp.idSector = $idSector "
								. "HAVING distancia < $km ORDER BY distancia ASC";
      $data = DB::select(DB::raw($query));
			for($i = 0; $i < count($data); $i++){
				$val = "";
				$ser = Servicio::select("nombre")->where("idEmpresa","=",$data[$i]->id)->get();
				$tam = count($ser);
				for($j = 0; $j < $tam; $j++){
					$val .= $ser[$j]->nombre;
					if(($j + 1) < $tam){
						$val .= ",";
					}
				}
				$data[$i]->servicios = $val;
			}
	    $response->getBody()->write(json_encode($data));
	    return $response;
	}


  	function postSector(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $sector = new Sector;
            $sector->nombre   	 =  $data['nombre'];
            $sector->descripcion =  $data['descripcion'];
            $sector->estado		 =  "INACTIVO";
            $sector->save();
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
