<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Database\Capsule\Manager as DB;

class EmpleadoControl{
  function getAll(Request $request, Response $response) {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = Empleado::all();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  function getById(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $data = Empleado::select("empleado.*","perfil.nombre as admin")
                  ->join("perfil","perfil.id","=","idPerfil")
                  ->where("empleado.id","=",$id)
                  ->get();
    $response->getBody()->write($data);
    return $response;
  }

  function post(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);

    try{
        $empleado = new Empleado;
        $empleado->idSucursal       =   $data['idSucursal'];
        $empleado->identificacion   =   $data['identificacion'];
        $empleado->email            =   $data['email'];
        $empleado->nombres          =   $data['nombres'];
        $empleado->apellidos        =   $data['apellidos'];
        $empleado->telefono         =   $data['telefono'];
        $empleado->pass             =   sha1($data['pass']);
        $empleado->idPerfil         =   '2';
        $empleado->estado           =   "ACTIVO";
        $empleado->estadoOnline     =   "INACTIVO";
        $empleado->save();

        for($i=0; $i< count($data['servicios']);$i++){
          $servicio = new ServiciosEmpleado;
          $servicio->idEmpleado =  $empleado->id;
          $servicio->idServicio =  $data['servicios'][$i]['idServicio'];               
          $servicio->save();
        }

        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
        $response = $response->withStatus(200);
    }catch(Exception $err){
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function delete(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                      ->where("id","=",$id)
                      ->get();
      $empleado->estado = "INACTIVO";
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Eliminado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function update(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->identificacion   =   $data['identificacion'];
      $empleado->email            =   $data['email'];
      $empleado->nombres          =   $data['nombres'];
      $empleado->apellidos        =   $data['apellidos'];
      $empleado->telefono         =   $data['telefono'];
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function updatePass(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->pass   =   sha1($data['pass']);
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function sesion(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $data = Empleado::select("id","idSucursal","nombres","apellidos","email","telefono","identificacion","idPerfil")
                    ->where("pass","=",sha1($data['pass']))
                    ->where("identificacion","=",$data['identificacion'])
                    ->first();
    $respuesta = json_encode(array('empleado' => $data, "std" => $data->idPerfil));
    if($data == null){
      $respuesta = json_encode(array('empleado' => null, "std" => 0));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function updatePush(Request $request, Response $response){
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->idPush   =   $data['idpush'];
      $empleado->save();
      $respuesta = json_encode(array('msg' => "idpus registrado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function getEmpleadosBySucursal(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idSucursal = $request->getAttribute("idSucursal");
    $query = "SELECT "
              . "emp.id as idEmpleado,"
              . "ser.id as idServicio,"
              . "CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,"
              . "ser.nombre as servicio, "
              . "'' as cliente, "
              . "'' as tiempoEstimado, "
              . "'' as turnoActual "
              . "FROM empleado emp "
              . "INNER JOIN "
              . "serviciosempleado seremp ON(seremp.idEmpleado = emp.id) "
              . "INNER JOIN "
              . "servicio ser ON(ser.id = seremp.idServicio) "
              . "WHERE emp.idSucursal = $idSucursal AND emp.estadoOnline = 'ACTIVO' AND ser.estado = 'ACTIVO'";
    $data = DB::select(DB::raw($query));
    for($i = 0; $i < count($data); $i++){
      //CALCULAR TIEMPO
      $query = "SELECT "
                ."TIME_FORMAT(SEC_TO_TIME((AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)) * turnosFaltantes.faltantes)),'%H:%i:%s') as tiempoEstimado, "
                ."COALESCE(turnoAct.turnoActual,0) as turnoActual "
                ."FROM "
                ."( "
                ."  SELECT "
                ."    count(t.id) as faltantes "
                ."    FROM "
                ."    turno as t "
                ."    WHERE "
                ."    t.idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."    t.idServicio = ".$data[$i]->idServicio." AND "
                ."    t.estadoTurno <> 'TERMINADO' AND t.estadoTurno <> 'CANCELADO'"
                .") as turnosFaltantes, "
                ."(SELECT MAX(tu.turno) as turnoActual FROM turno as tu WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND tu.estadoTurno = 'ATENDIENDO') as turnoAct,"
                ."turno "
                ."WHERE "
                ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."idServicio = ".$data[$i]->idServicio." AND "
                ."estadoTurno = 'TERMINADO' LIMIT 1";
        $dataTiempo = DB::select(DB::raw($query));
        $data[$i]->tiempoEstimado = $dataTiempo[0]->tiempoEstimado;
        if($data[$i]->tiempoEstimado == null){
          $data[$i]->tiempoEstimado = "00:00:00";
        }
        $data[$i]->turnoActual = $dataTiempo[0]->turnoActual;
        $query = "SELECT "
                  ."COALESCE(tu.turno,0) as turnoActual, "
                  ."COALESCE(CONCAT(cl.nombres,' ',cl.apellidos),'') as cliente "
                  ."FROM turno as tu "
                  ."INNER JOIN "
                  ."cliente as cl "
                  ."ON(cl.id = tu.idCliente) "
                  ."WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                  ."tu.idServicio = ".$data[$i]->idServicio." AND "
                  ."(tu.estadoTurno = 'ATENDIENDO' OR "
                  ."tu.estadoTurno = 'CONFIRMADO') ORDER BY tu.fechaSolicitud Asc LIMIT 1";
        $dataCliente = DB::select(DB::raw($query));
        if(count($dataTiempo) > 0){
          $data[$i]->tiempoEstimado = $dataTiempo[0]->tiempoEstimado;
        }
        if(count($dataCliente) > 0){
          $data[$i]->turnoActual = $dataCliente[0]->turnoActual;
          $data[$i]->cliente = $dataCliente[0]->cliente;
        }
        if($data[$i]->tiempoEstimado == null){
          $data[$i]->tiempoEstimado = "00:00:00";
        }
        
        if($data[$i]->turnoActual == null){
          $data[$i]->turnoActual = 0;
        }
        
        if($data[$i]->cliente == null){
          $data[$i]->cliente = "";
        }
    }
    $response->getBody()->write(json_encode($data));
    return $response;
  }

  function sesionlogin(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $data = Empleado::select("empleado.id","empleado.idSucursal","empleado.nombres","empleado.apellidos","empleado.email","empleado.telefono","empleado.identificacion","empleado.idPerfil","empleado.idEmpresa","perfil.nombre as admin")
                    ->join("perfil","perfil.id","=","empleado.idPerfil")
                    ->where("pass","=",sha1($data['pass']))
                    ->where("identificacion","=",$data['identificacion'])
                    ->where("empleado.estado","=","ACTIVO")
                    ->first();
    $respuesta = json_encode(array('admin' => $data, "std" => 1));

      if($data == null){
        $respuesta = json_encode(array('admin' => null, "std" => 0));
        $response = $response->withStatus(404);
      }

      $response->getBody()->write($respuesta);
      return $response;
  }

    function postsuperadmin(Request $request, Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      try{
          $empleado = new Empleado;
          $empleado->nombres          =   $data['nombres'];
          $empleado->apellidos        =   $data['apellidos'];
          $empleado->identificacion   =   $data['identificacion'];
          $empleado->pass             =   sha1($data['pass']);
          $empleado->idperfil         =   $data['idperfil'];
          $empleado->email           =   $data['email'];
          $empleado->estado           =   "ACTIVO";
          $empleado->save();
          $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
          $response = $response->withStatus(200);
      }catch(Exception $err){
          $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
    }

    function updateadminestado(Request $request,Response $response){
        try{
          $response = $response->withHeader('Content-type', 'application/json');
          $data = json_decode($request->getBody(),true);
          $id = $request->getAttribute("id");
          $admin = Empleado::select("*")
                        ->where("idEmpresa","=",$id)
                        ->first();
          $admin->estado   =   $data['estado'];
          $admin->save();
          $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
          $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function updateadminestadodesactivar(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        try{
            $id = $request->getAttribute("id");
            $admin = Empleado::select("*")
                          ->where("idempresa","=",$id)
                          ->first();
            $admin->estado   =   $data['estado'];
            $admin->save();
            $respuesta = json_encode(array('msg' => "Actualizado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
        $response->getBody()->write($respuesta);
        return $response;
    }

    function getEmpleadoByIdsucursal(Request $request,Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $data = Empleado::select("*")
                      ->where("idSucursal","=",$id)
                      ->where('idPerfil','=','4')
                      ->get();
      $response->getBody()->write($data);
      return $response;
    }

    function getEmpleadoByIdsucursalempleado(Request $request,Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $data = Empleado::select("*")
                      ->where("idSucursal","=",$id)
                      ->where('idPerfil','=','2')
                      ->get();
      $response->getBody()->write($data);
      return $response;
    }

    function getempresaxByIdsucursal(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $id = $request->getAttribute("id");
        $data = Empleado::select("empleado.idSucursal","empresa.razonSocial","empresa.id")
                  ->join("sucursal","sucursal.id","=","empleado.idSucursal")
                  ->join("empresa","empresa.id","=","sucursal.idEmpresa")
                  ->where("empleado.idSucursal","=",$id)
                  ->first();
        $response->getBody()->write($data);
        return $response;
    }

    function maxId(Request $request,Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $data = Empleado::select("")
                        ->max('id');
        $response->getBody()->write($data);
        return $response;
    }

    function updateestado(Request $request,Response $response){
      try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->estado   =   $data['estado'];
      $empleado->save();
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
    }

    public function getEmpleadosAll(Request $request, Response $response)
    {
      $idEmpleado= $request->getAttribute("idEmpleado");
      $query = "SELECT emp.id as idEmpleado,ser.id as idServicio,CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,ser.nombre as servicio, '' as tiempoEstimado, '' as cliente, '' as turnoActual, su.nombre as sucursal, em.razonSocial as Empresa, su.id as idSucursal FROM empleado emp INNER JOIN serviciosempleado seremp ON(seremp.idEmpleado = emp.id) INNER JOIN servicio ser ON(ser.id = seremp.idServicio) INNER JOIN sucursal su ON(su.id = emp.idSucursal) INNER JOIN empresa em ON(em.id = su.idEmpresa) WHERE emp.id = $idEmpleado AND emp.estadoOnline = 'ACTIVO' AND ser.estado = 'ACTIVO'";
      $data = DB::select(DB::raw($query));
      for($i = 0; $i < count($data); $i++){
        //CALCULAR TIEMPO
        $query = "SELECT "
                  ."TIME_FORMAT(SEC_TO_TIME((AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)) * ( "
                  ."  SELECT "
                  ."    count(t.id) as faltantes "
                  ."    FROM "
                  ."    turno as t "
                  ."    WHERE "
                  ."    t.idEmpleado = ".$data[$i]->idEmpleado." AND "
                  ."    t.idServicio = ".$data[$i]->idServicio." AND "
                  ."    t.estadoTurno <> 'TERMINADO' AND "
                  ."    t.estadoTurno <> 'CANCELADO' "
                  ."))),'%H:%i:%s') as tiempoEstimado "
                  ."FROM "
                  ."turno "
                  ."WHERE idEmpleado = ".$data[$i]->idEmpleado." AND "
                  ."idServicio = ".$data[$i]->idServicio." AND "
                  ."estadoTurno = 'TERMINADO' "
                  ."LIMIT 1";
          $dataTiempo = DB::select(DB::raw($query));
          $query = "SELECT "
                    ."COALESCE(MAX(tu.turno),0) as turnoActual, "
                    ."COALESCE(CONCAT(cl.nombres,' ',cl.apellidos),'') as cliente "
                    ."FROM turno as tu "
                    ."INNER JOIN "
                    ."cliente as cl "
                    ."ON(cl.id = tu.idCliente) "
                    ."WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                    ."tu.idServicio = ".$data[$i]->idServicio." AND "
                    ."tu.estadoTurno = 'ATENDIENDO' OR "
                    ."tu.estadoTurno = 'CONFIRMADO' LIMIT 1";
          $dataCliente = DB::select(DB::raw($query));
          $data[$i]->tiempoEstimado = $dataTiempo[0]->tiempoEstimado;
          if($data[$i]->tiempoEstimado == null){
            $data[$i]->tiempoEstimado = "00:00:00";
          }
          $data[$i]->turnoActual = $dataCliente[0]->turnoActual;
          $data[$i]->cliente = $dataCliente[0]->cliente;
      }
      $response->getBody()->write(json_encode($data));
      return $response;
    }

}