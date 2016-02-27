<?php
use Slim\Http\Request;
use Slim\Http\Response;

class ServiciosSectorControl{

	function getServicioSector(Request $request, Response $response){
	$response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        	$servi = ServiciosSector::select('serviciossector.id','serviciossector.idSector','serviciossector.idServicio','sector.nombre as sector',
        		'servicio.nombre as servicio')
        		->join('sector','sector.id','=','serviciossector.idSector')
        		->join('servicio','servicio.id','=','serviciossector.idServicio')
        		->where('serviciossector.idSector','=',$id)
        		->get();
		    $response->getBody()->write($servi);
	    	return $response;
	}

}