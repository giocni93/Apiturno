<?php
use Slim\Http\Request;
use Slim\Http\Response;

class ServicioControl{

	function postServicios(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	        $servicio = new Servicio;
	        $servicio->nombre 	   =  $data['nombre'];
	        $servicio->descripcion =  $data['descripcion'];
	        $servicio->estado      =  "ACTIVO";
	        $servicio->save();
	        //$val = $servicio->id;

	        $servisector = new ServiciosSector;
	        $servisector->idSector		=	$data['idSector'];
	        $servisector->idServicio	= 	$servicio->id;
	        $servisector->save();

	        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
	        $response = $response->withStatus(200);
	    }catch(Exception $err){
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
	}

	function getAll(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Servicio::select('*')
								->where('estado','=','ACTIVO')
								->get();
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

		function getServiciosBySucursal(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
        $idSucursal = $request->getAttribute("idSucursal");
				$data = Servicio::select('servicio.id as idServicio',
																"servicio.nombre as servicio",
																"servicio.id as turnoActual",
																"empleado.nombres as empleado",
																"empleado.apellidos as apeEmp")
					->join("serviciossucursal","serviciossucursal.idServicio","=","servicio.id")
					->join("serviciosempleado","serviciosempleado.idServicio","=","servicio.id")
					->join("empleado","serviciosempleado.idEmpleado","=","empleado.id")
		    	->where('serviciossucursal.idSucursal','=',$idSucursal)
		    	->where('servicio.estado','=','ACTIVO')
		    	->get();
		    if(count($data) == 0){
		      $response = $response->withStatus(404);
		    }else{
					for($i = 0; $i < count($data); $i++){
							$tur = Turno::select('turno.turno')
							    	->where('turno.idSucursal','=',$idSucursal)
										->where('turno.idServicio','=',$data[$i]->idServicio)
										->where(function($q){
											$q->where('turno.estadoTurno','=','CONFIRMADO')
											->orwhere('turno.estadoTurno','=','ATENDIENDO');
										})
										->orderBy("turno.fechaSolicitud","Asc")
							    	->first();
							$turno = 0;
							if($tur != null){
								$turno = $tur->turno;
							}
							$data[$i]->empleado = $data[$i]->empleado." ".$data[$i]->apeEmp;
							$data[$i]->turnoActual = $turno;
					}
				}
		    $response->getBody()->write($data);
		    return $response;
  	}

  	public function getServiciosBySector(Request $request, Response $response)
  	{
  		$response = $response->withHeader('Content-type', 'application/json');
        $idSector = $request->getAttribute("idSector");
        $data = Servicio::select("servicio.*")
        		->join("serviciossector", "serviciossector.idServicio", "=", "servicio.id")
        		->join("sector", "serviciossector.idSector", "=", "sector.id")
        		->where("sector.id", $idSector)
        		->get();
        $response->getBody()->write($data);
		return $response;
  	}

		function getServiciosByEmpleado(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
			$idEmpleado = $request->getAttribute("idEmpleado");
			$data = Servicio::select("servicio.id as idServicio","servicio.nombre as servicio","servicio.nombre as turnoActual")
					->join("serviciosempleado", "serviciosempleado.idServicio", "=", "servicio.id")
					->where("serviciosempleado.idEmpleado","=", $idEmpleado)
					->get();
		    if(count($data) == 0){
		      $response = $response->withStatus(404);
		    }else{
					for($i = 0; $i < count($data); $i++){
							$tur = Turno::select('turno.turno')
										->where('turno.idServicio','=',$data[$i]->idServicio)
										->where(function($q){
											$q->where('turno.estadoTurno','=','CONFIRMADO')
											->orwhere('turno.estadoTurno','=','ATENDIENDO');
										})
										->orderBy("turno.fechaSolicitud","Asc")
							    	->first();
							$turno = 0;
							if($tur != null){
								$turno = $tur->turno;
							}
							$data[$i]->turnoActual = $turno;
					}
				}
		    $response->getBody()->write($data);
		    return $response;
  	}


}
