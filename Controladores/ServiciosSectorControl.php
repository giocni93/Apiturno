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

        function getsectorxsucursales(Request $request, Response $response){
                $response = $response->withHeader('Content-type', 'application/json');
                $id = $request->getAttribute("id");
                $servi = ServiciosSector::select('serviciossector.*')
                        ->where('serviciossector.idSector','=',$id)
                        ->groupBy('serviciossector.idSector')
                        ->get();
                        for($i=0;$i<count($servi);$i++){
                                $sucu = ServiciosSucursal::select('serviciossucursal.idSucursal','sucursal.nombre','sucursal.latitud','sucursal.longitud','sucursal.estado','sucursal.direccion','sucursal.telefono','empresa.razonSocial')
                                        ->join('sucursal','sucursal.id','=','serviciossucursal.idSucursal')
                                        ->join('empresa','empresa.id','=','sucursal.idEmpresa')
                                        ->where('serviciossucursal.idServicio','=',$servi[$i]->idServicio)
                                        ->get();
                                $servi[$i]['servicio'] = $sucu;
                        }

                $response->getBody()->write($servi);
                return $response;
        }

      

}