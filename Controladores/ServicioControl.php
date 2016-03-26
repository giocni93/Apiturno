<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class ServicioControl{

	function postServicios(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    try{
	        $servicio = new Servicio;
	        $servicio->nombre 	   =  $data['nombre'];
	        $servicio->descripcion =  $data['descripcion'];
	        $servicio->estado      =  $data['estado'];
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

  	function getAllservis(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
	    $data = Servicio::select('servicio.*','serviciossector.*','sector.nombre as sector')
	    					->join('serviciossector','serviciossector.idServicio','=','servicio.id')
	    					->join('sector','sector.id','=','serviciossector.idSector')
	    					->orderBy('sector','ASC')
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
            $data = Servicio::select('servicio.*')
                ->join("serviciossucursal","serviciossucursal.idServicio","=","servicio.id")
                ->where('serviciossucursal.idSucursal','=',$idSucursal)
                ->where('servicio.estado','=','ACTIVO')
                ->get();
            if(count($data) == 0){
              $response = $response->withStatus(404);
            }else{
                for($i = 0; $i < count($data); $i++){
                    $tur = Turno::select('turno.turno')
                        ->join("empleado","empleado.id","=","turno.idEmplado")
                        ->where('turno.idSucursal','=',$idSucursal)
                        ->where('turno.idServicio','=',$data[$i]->idServicio)
                        ->where('turno.estadoTurno','=','CONFIRMADO')
                        ->orwhere('turno.estadoTurno','=','ATENDIENDO')
                        ->orderBy("turno.fechaSolicitud","Desc")
                        ->first();
                        $turno = 1;
                    if($tur != null){
                        $turno = $tur->turno;
                    }
                }
            }
            $response->getBody()->write($data);
            return $response;
  	}

        function getServiciosByEmpleado(Request $request, Response $response){
            $response = $response->withHeader('Content-type', 'application/json');
            $idEmpleado = $request->getAttribute("idEmpleado");
            $data = Servicio::select('servicio.*')
                ->join("serviciosempleado","serviciosempleado.idServicio","=","servicio.id")
                ->where('serviciosempleado.idEmpleado','=',$idEmpleado)
                ->where('servicio.estado','=','ACTIVO')
                ->get();
            if(count($data) == 0){
              $response = $response->withStatus(404);
            }else{
                for($i = 0; $i < count($data); $i++){
                    $query = "SELECT "
                    . "Count(tu.turno) as numeroTurno "
                    . "FROM "
                    . "turno as tu "
                    . "WHERE tu.idEmpleado = ".$idEmpleado." AND "
                    . "tu.idServicio = ".$data[$i]->id." AND "
                    . "(tu.estadoTurno <> 'TERMINADO' AND tu.estadoTurno <> 'CANCELADO')";
                    $dataNumeroTurno = DB::select(DB::raw($query));
                    $data[$i]['numeroTurno'] = $dataNumeroTurno[0]->numeroTurno;
                    $data[$i]['turnoActual'] = 0;
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

    public function getPreciosByServicioSucursal(Request $request, Response $response)
    {
        $response = $response->withHeader('Content-type', 'application/json');
        $idServicio = $request->getAttribute("idServicio");
        $idSucursal = $request->getAttribute("idSucursal");
        $data = ServiciosSucursal::select("*")
            ->where("idServicio","=", $idServicio)
            ->where("idSucursal","=", $idSucursal)
            ->first();
        if($data == null){
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($data);
        return $response;
    }

    function serviciossucursales(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $idempresa = $request->getAttribute("idempresa");

        $empresa = SectorEmpresa::select('sectorempresa.idSector')
                            ->where('sectorempresa.idEmpresa','=',$idempresa)
                            ->get();
        for ($i=0;$i<count($empresa);$i++) {
            $servi = ServiciosSector::select('serviciossector.idServicio','servicio.nombre',
                                    'servicio.descripcion','servicio.estado',
                                    'sector.nombre as sector')
                            ->join('servicio','servicio.id','=','serviciossector.idServicio')
                            ->join('sector','sector.id','=','serviciossector.idSector')
                            ->where('serviciossector.idSector','=',$empresa[$i]->idSector)
                            ->get();
            $empresa[$i]['servicio'] = $servi;
        }


        $response->getBody()->write($empresa);
        return $response;

    }

    function getAllservicios(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $data = Servicio::select('id as idServicio','nombre')
                            ->where('estado','=','ACTIVO')
                            ->get();
	    if(count($data) == 0){
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($data);
	    return $response;
  	}

		function calificarServicio(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $idServicio = $request->getAttribute("idServicio");
        $data = json_decode($request->getBody(),true);
        try{
            $query = "INSERT INTO "
                    . "calificacionservicio (idServicio,idEmpleado,idSucursal,calificacion) "
                    . "VALUES ("
                    . "$idServicio,"
                    . "".$data['idEmpleado'].","
                    . "".$data['idSucursal'].","
                    . "".$data['calificacion'].")";
            DB::statement(DB::raw($query));

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
