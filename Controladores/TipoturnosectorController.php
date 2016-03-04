<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class TipoturnosectorControl{

	function gettipoturnosector(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = Tipoturnosector::all();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
	}

	function tiposelecionados(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Tipoturnosector::select("*")
                    ->where("idSector","=",$id)
                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}
	
}