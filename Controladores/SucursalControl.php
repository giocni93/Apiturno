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
	        $sucursal->longitud    =  $data['longitud'];
	        $sucursal->promedio    =  $data['promedio'];
	        //$sucursal->usuario	   =  $data['usuario'];
	        //$sucursal->pass		   =  sha1($data['pass']);	
	        $sucursal->estado      =  "ACTIVO";
	        $sucursal->save();
	        $val = $sucursal->id;

	        $admin = new Empleado;
	        $admin->nombres       	=   $data['nombres'];
	        $admin->apellidos		= 	$data['apellidos'];
	        $admin->identificacion  = 	$data['identificacion'];
	        $admin->pass            =   sha1($data['pass']);
	        $admin->telefono		= 	$data['telefonoadmin'];
	        $admin->estado          =   "ACTIVO";
	        $admin->idperfil		=  	'4';
	        $admin->idSucursal		= 	$sucursal->id;
	        $admin->email 			=	$data['email'];
	        $admin->save();

	        /*for($i=0; $i< count($data['servicios']);$i++){
	    		$servicio = new ServiciosSucursal;
	    		$servicio->idServicio =	 $data['servicios'][$i][0]['idServicio'];
	    		$servicio->idSucursal =  $val;    					 
	    		$servicio->save();
	    	}*/

	        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
	        $response = $response->withStatus(200);
	    }catch(Exception $err){
	        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	        $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
	}

	function getAllsucursalesId(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Sucursal::select("sucursal.*","empresa.razonSocial")
	    				->join('empresa','empresa.id','=','sucursal.idEmpresa')
	                    ->where("idEmpresa","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
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

    function maxIdsucursal(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Sucursal::select("")
                        ->max('id');
        $response->getBody()->write($data);
        return $response;
	}

		function getSucursalesByPosicion(Request $request, Response $response){
				$response = $response->withHeader('Content-type', 'application/json');
				//$data = Parametros::select("*")->first();
				//$km = $data["diametro_busqueda"];
				$idServicio = $request->getAttribute("idServicio");
				$lat = $request->getAttribute("latitud");
				$lng = $request->getAttribute("longitud");
				$query = "SELECT "
	                . "(6371 * ACOS( SIN(RADIANS(su.latitud)) * SIN(RADIANS($lat)) + COS(RADIANS(su.longitud - $lng)) * "
					. "COS(RADIANS(su.latitud)) * COS(RADIANS($lat)))) AS distancia, "
					. "su.*, "
					. "ss.precio, "
					. "ss.precioVIP "
	                . "FROM sucursal su "
	                . "INNER JOIN serviciossucursal ss ON ss.idSucursal = su.id "
	                . "INNER JOIN servicio se ON se.id = ss.idServicio "
	                . "WHERE su.Estado = 'ACTIVO' AND se.id= $idServicio "
					. "HAVING distancia < 2 ORDER BY distancia ASC";
	      $data = DB::select(DB::raw($query));
		    $response->getBody()->write(json_encode($data));
		    return $response;
		}

	public function getSucursalesByCiudad(Request $request, Response $response)
	{
		$response = $response->withHeader('Content-type', 'application/json');
		$idServicio = $request->getAttribute("idServicio");
		$ciudad = $request->getAttribute("ciudad");
                $lat = $request->getAttribute("latitud");
		$lng = $request->getAttribute("longitud");
		$dataCiudad = Municipio::select("*")->where("nombre","=",$ciudad)->first();
		$idMunicipio = $dataCiudad->id;

		$query = "SELECT "
                        . "(6371 * ACOS( SIN(RADIANS(su.latitud)) * SIN(RADIANS($lat)) + COS(RADIANS(su.longitud - $lng)) * "
			. "COS(RADIANS(su.latitud)) * COS(RADIANS($lat)))) AS distancia, "
                        . "0 as numeroTurnos, "
                        . "su.*, "
                        . "ss.precio, "
                        . "ss.precioVIP "
	                . "FROM sucursal su "
	                . "INNER JOIN serviciossucursal ss ON ss.idSucursal = su.id "
	                . "INNER JOIN servicio se ON se.id = ss.idServicio "
	                . "INNER JOIN municipio mu ON mu.id = su.idMunicipio "
	                . "WHERE su.Estado = 'ACTIVO' AND se.id= $idServicio "
	                . "AND mu.id = $idMunicipio ORDER BY distancia ASC";
	    $data = DB::select(DB::raw($query));
            for($i = 0; $i < count($data); $i++){
                $query = "SELECT "
                        . "count(tu.id) as numTurnos "
                        . "FROM turno as tu "
                        . "WHERE tu.idServicio = $idServicio AND "
                        . "tu.idSucursal = ".$data[$i]->id." AND "
                        . "(tu.estadoTurno <> 'TERMINADO' AND "
                        . "tu.estadoTurno <> 'CANCELADO')";
                $dataTur = DB::select(DB::raw($query));
                if(count($dataTur) > 0){
                    $data[$i]->numeroTurnos = $dataTur[0]->numTurnos;
                }
            }
            $response->getBody()->write(json_encode($data));
            return $response;

	}

	function getsucursalxempresa(Request $request, Response $response){
		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Sucursal::select("*")
	                    ->where("idEmpresa","=",$id)
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
	}

	function putsucursal(Request $request, Response $response){

		$response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $idEmpleado = $request->getAttribute("idEmpleado");
            $sucursal = Sucursal::find($id);
            $sucursal->nombre   	=       $data['nombre'];
            $sucursal->direccion    	=       $data['direccion'];
            $sucursal->telefono         =       $data['telefono'];
            $sucursal->idMunicipio      =       $data['idMunicipio'];
            $sucursal->latitud     	=  	$data['latitud'];
            $sucursal->longitud    	=  	$data['longitud'];
            //$sucursal->promedio    	=  	$data['promedio'];
            $sucursal->save();

            $admin = Empleado::select("*")
                        ->where("id","=",$idEmpleado)
                        ->first();
	        $admin->nombres       	=       $data['nombres'];
	        $admin->apellidos	= 	$data['apellidos'];
	        $admin->identificacion  = 	$data['identificacion'];
	        $admin->telefono	= 	$data['telefonoadmin'];
	        $admin->email 		=	$data['email'];
	        $admin->save();

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
            $sector = Sucursal::find($id);
            $sector->estado   	 =   $data['estado'];
            $sector->save();

            $admin = Empleado::select("*")
                          ->where("idSucursal","=",$id)
                          ->first();
            $admin->estado   =   $data['estado'];
            $admin->save();

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
            $sector = Sucursal::find($id);
            $sector->estado   	 =   $data['estado'];
            $sector->save();
            $admin = Empleado::select("*")
                          ->where("idSucursal","=",$id)
                          ->first();
            $admin->estado   =   $data['estado'];
            $admin->save();
            $respuesta = json_encode(array('msg' => "Desactivado correctamente", "std" => 1));
      		$response = $response->withStatus(200);
		} catch (Exception $err) {
	      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
	      $response = $response->withStatus(404);
	    }
	    $response->getBody()->write($respuesta);
	    return $response;
  	}

  	function sectoresempresa(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
	    $data = json_decode($request->getBody(),true);
	    $data = Sucursal::select("administrador.id","administrador.nombres","administrador.apellidos","administrador.identificacion","administrador.estado","perfil.nombre as nombreperfil","perfil.id as idperfil","administrador.correo","administrador.idempresa")
	    				->join("perfil","perfil.id","=","administrador.idperfil")
	    				->join("perfilpermisos","perfilpermisos.idperfil","=","perfil.id")
	    				->where("pass","=",sha1($data['pass']))
	                    ->where("identificacion","=",$data['identificacion'])
	                    ->where("administrador.estado","=","ACTIVO")
	                    ->first();

	    $respuesta = json_encode(array('admin' => $data, "std" => 1));

	    if($data == null){
	      $respuesta = json_encode(array('admin' => null, "std" => 0));
	      $response = $response->withStatus(404);
	    }

	    $response->getBody()->write($respuesta);
	    return $response;
  	}

  	function getAllsucursalesxempresa(Request $request, Response $response) {
	    $response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $data = Sucursal::select("sucursal.*","empresa.razonSocial")
	    				->join('empresa','empresa.id','=','sucursal.idEmpresa')
	                    ->get();
	    $response->getBody()->write($data);
	    return $response;
  	}

  	function empleadosporempresa(Request $request, Response $response){
  		$response = $response->withHeader('Content-type', 'application/json');
	    $id = $request->getAttribute("id");
	    $sucursal = Sucursal::select('sucursal.id','sucursal.nombre as sucursal','empresa.razonSocial')
	    					->join('empresa','empresa.id','=','sucursal.idEmpresa')
	    					->where('sucursal.idEmpresa','=',$id)
	    					->get();
	    			for($i=0;$i<count($sucursal);$i++){
	    				$empleado = Empleado::select('empleado.nombres','empleado.apellidos',
	    					'empleado.identificacion','empleado.estado','empleado.id',
                                                'empleado.telefono','empleado.email')
	    					->where('empleado.idSucursal','=',$sucursal[$i]->id)
	    					->where('empleado.idPerfil','=',2)
	    					->get();
	    				$sucursal[$i]['empleados'] = $empleado;
	    			}
	    $response->getBody()->write($sucursal);
	    return $response;					
  	}
        
    function sucursalesactivas(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Sucursal::select("sucursal.*")
                        ->where('estado','=','ACTIVO')
                        ->get();
        $response->getBody()->write($data);
        return $response;
    }
    
     function sucursalebyempresaactivas(Request $request, Response $response) {
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = Sucursal::select("sucursal.*","empresa.razonSocial")
                                    ->join('empresa','empresa.id','=','sucursal.idEmpresa')
                        ->where("idEmpresa","=",$id)
                        ->where('sucursal.estado','=','ACTIVO')
                        ->get();
        $response->getBody()->write($data);
        return $response;
    }
    
    function getId(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = Sucursal::find($id);
        $response->getBody()->write($data);
        return $response;
    }

}