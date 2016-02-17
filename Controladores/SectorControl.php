<?php
use Slim\Http\Request;
use Slim\Http\Response;

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

	function getSectorEmpresas(Request $request, Response $response)
	{
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Empresa::select("empresa.*")
	    				->join("sectorempresa","sectorempresa.idEmpresa","=","empresa.id")
	    				->join("sector","sector.id","=","sectorempresa.idSector")
	                    ->where("sector.id","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}

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
