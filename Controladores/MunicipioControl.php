<?php
use Slim\Http\Request;
use Slim\Http\Response;

class MunicipiosControl{

	function getAll(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Municipio::all();
        if(count($data) == 0){
          $response = $response->withStatus(404);
        }
        $response->getBody()->write($data);
        return $response;
    }

    function getMunicipioId(Request $request, Response $response){
    	$response = $response->withHeader('Content-type', 'application/json');
    	$id = $request->getAttribute("id");
	    $data = Municipio::select("*")
	                    ->where("idDepartamento","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
    }

    function getMunicipioDepartamento(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = Municipio::select("municipio.*","departamento.nombre as departamento")
                        ->join('departamento','departamento.id','=','municipio.idDepartamento')
                        ->where("municipio.id","=",$id)
                        ->first();
        $response->getBody()->write($data);
        return $response;
    }

}