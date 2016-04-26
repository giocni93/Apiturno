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
  
  function getallactivos(Request $request, Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
        $data = Empleado::select('empleado.nombres','empleado.apellidos','empleado.telefono','empleado.email',
                'empleado.identificacion','empleado.id','sucursal.nombre as sucursal','empleado.estado',
                'sucursal.id as idsucursal','empresa.razonSocial','empresa.id as idempresa')
                    ->join('sucursal','sucursal.id','=','empleado.idSucursal')
                    ->join('empresa','empresa.id','=','sucursal.idEmpresa')
                    ->where('empleado.idperfil','=','2')
                    ->get();
        
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
  
  function getEstadoEmpleado(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("idEmpleado");
    $data = Empleado::select("estadoOnline")
                  ->where("id","=",$id)
                  ->first();
    if($data == null){
      $response = $response->withStatus(404);
    }
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
        $empleado->logo             =   '/imagenes/users-10.png';
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
  
  function postEmpleado(Request $request, Response $response){
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
        $empleado->logo             =   '/imagenes/users-10.png';
        $empleado->idPerfil         =   '2';
        $empleado->estado           =   "ACTIVO";
        $empleado->estadoOnline     =   "INACTIVO";
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
      $empleado->idSucursal       =   $data['idSucursal'];
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

  public function updateEstadoEmpleado(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("idEmpleado");
      $empleado = Empleado::select("*")
                          ->where("id","=",$id)
                          ->first();
      $empleado->estadoOnline   =   $data['estadoOnline'];
      $empleado->save();
      $est = "En linea";
      if($data['estadoOnline'] == "INACTIVO"){
          $est = "Desconectado";
      }
      $respuesta = json_encode(array('msg' => "Modificado correctamente", "std" => 1, "estadoOnline" => $est));
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
    $idServicio = $request->getAttribute("idServicio");
    $query = "SELECT DISTINCT "
              . "emp.id as idEmpleado,"
              . "ser.id as idServicio,"
              . "CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,"
              . "ser.nombre as servicio, "
              . "'' as cliente, "
              . "'' as tiempoEstimado, "
              . "'' as numeroTurno, "
              . "'' as TipoTurno, "
              . "'' as turnoActual "
              . "FROM empleado emp "
              . "INNER JOIN "
              . "serviciosempleado seremp ON(seremp.idEmpleado = emp.id) "
              . "INNER JOIN "
              . "servicio ser ON(ser.id = seremp.idServicio) "
              . "WHERE emp.idSucursal = $idSucursal  AND ser.id = $idServicio AND ser.estado = 'ACTIVO' AND emp.estadoOnline = 'ACTIVO'";
    $data = DB::select(DB::raw($query));
    for($i = 0; $i < count($data); $i++){
      //CALCULAR TIEMPO
      $query = "SELECT "
                ."((COALESCE(AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)),0) * turnosFaltantes.faltantes)) as tiempoEstimado, "
                . "turnosFaltantes.faltantes as turnFaltantes "
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
                ."turno "
                ."WHERE "
                ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."idServicio = ".$data[$i]->idServicio." AND "
                ."estadoTurno = 'TERMINADO' LIMIT 1";
        $dataTiempo = DB::select(DB::raw($query));
        $query = "SELECT "
                . "Count(tu.turno) as numeroTurno "
                . "FROM "
                . "turno as tu "
                . "WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                . "tu.idServicio = ".$data[$i]->idServicio." AND "
                . "(tu.estadoTurno <> 'TERMINADO' AND tu.estadoTurno <> 'CANCELADO')";
        $dataNumeroTurno = DB::select(DB::raw($query));
        $query = "SELECT "
                    . "tu.turno as turnoActual, "
                    . "CONCAT(cl.nombres,' ',cl.apellidos) as cliente, "
                    . "tu.tipoTurno as tipoTurno "
                    . "FROM turno as tu "
                    . "INNER JOIN tipoturno tt "
                    . "ON (tt.id = tu.tipoTurno) "
                    . "INNER JOIN cliente cl "
                    . "ON(cl.id = tu.idCliente) "
                    . "WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                    . "tu.idServicio = ".$data[$i]->idServicio." AND "
                    . "(tu.estadoTurno = 'ATENDIENDO' OR "
                    . "tu.estadoTurno = 'CONFIRMADO') "
                    . "ORDER BY "
                    . "tu.turnoReal ASC "
                    . "LIMIT 1";
        $dataTurnoCliente= DB::select(DB::raw($query));
        if(count($dataTiempo) > 0){
            $val = ceil(($dataTiempo[0]->tiempoEstimado / 60));
            $str = " minuto";
            if($val == 0){
                //5 min
                $val = ceil(5 * $dataTiempo[0]->turnFaltantes);
            }
            if($val != 1){
                $str .= "s";
            }
            $data[$i]->tiempoEstimado = strval($val).$str;
        }else{
            $data[$i]->tiempoEstimado = "0 minutos";
        }
        
        $data[$i]->turnoActual = 0;
        $data[$i]->TipoTurno = 1;
        $data[$i]->cliente = '';
        if(count($dataTurnoCliente) > 0){
            $data[$i]->turnoActual = $dataTurnoCliente[0]->turnoActual;
            $data[$i]->TipoTurno = $dataTurnoCliente[0]->tipoTurno;
            $data[$i]->cliente = $dataTurnoCliente[0]->cliente;
        }
        $data[$i]->numeroTurno = $dataNumeroTurno[0]->numeroTurno;
    }
    $response->getBody()->write(json_encode($data));
    return $response;
  }
  
  function getEmpleadosBySucursal2(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idSucursal = $request->getAttribute("idSucursal");
    $query = "SELECT DISTINCT "
              . "emp.id as idEmpleado,"
              . "ser.id as idServicio,"
              . "CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,"
              . "ser.nombre as servicio, "
              . "'' as cliente, "
              . "'' as tiempoEstimado, "
              . "'' as numeroTurno, "
              . "'' as TipoTurno, "
              . "'' as turnoActual "
              . "FROM empleado emp "
              . "INNER JOIN "
              . "serviciosempleado seremp ON(seremp.idEmpleado = emp.id) "
              . "INNER JOIN "
              . "servicio ser ON(ser.id = seremp.idServicio) "
              . "WHERE emp.idSucursal = $idSucursal AND ser.estado = 'ACTIVO'";
    $data = DB::select(DB::raw($query));
    for($i = 0; $i < count($data); $i++){
      //CALCULAR TIEMPO
      $query = "SELECT "
                ."((COALESCE(AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)),0) * turnosFaltantes.faltantes)) as tiempoEstimado, "
                . "turnosFaltantes.faltantes as turnFaltantes "
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
                ."turno "
                ."WHERE "
                ."idEmpleado = ".$data[$i]->idEmpleado." AND "
                ."idServicio = ".$data[$i]->idServicio." AND "
                ."estadoTurno = 'TERMINADO' LIMIT 1";
        $dataTiempo = DB::select(DB::raw($query));
        $query = "SELECT "
                . "Count(tu.turno) as numeroTurno "
                . "FROM "
                . "turno as tu "
                . "WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                . "tu.idServicio = ".$data[$i]->idServicio." AND "
                . "(tu.estadoTurno <> 'TERMINADO' AND tu.estadoTurno <> 'CANCELADO')";
        $dataNumeroTurno = DB::select(DB::raw($query));
        $query = "SELECT "
                    . "tu.turno as turnoActual, "
                    . "CONCAT(cl.nombres,' ',cl.apellidos) as cliente, "
                    . "tu.tipoTurno as tipoTurno "
                    . "FROM turno as tu "
                    . "INNER JOIN tipoturno tt "
                    . "ON (tt.id = tu.tipoTurno) "
                    . "INNER JOIN cliente cl "
                    . "ON(cl.id = tu.idCliente) "
                    . "WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                    . "tu.idServicio = ".$data[$i]->idServicio." AND "
                    . "(tu.estadoTurno = 'ATENDIENDO' OR "
                    . "tu.estadoTurno = 'CONFIRMADO') "
                    . "ORDER BY "
                    . "tu.turnoReal ASC "
                    . "LIMIT 1";
        $dataTurnoCliente= DB::select(DB::raw($query));
        if(count($dataTiempo) > 0){
            $val = ceil(($dataTiempo[0]->tiempoEstimado / 60));
            $str = " minuto";
            if($val == 0){
                //5 min
                $val = ceil(5 * $dataTiempo[0]->turnFaltantes);
            }
            if($val != 1){
                $str .= "s";
            }
            $data[$i]->tiempoEstimado = strval($val).$str;
        }else{
            $data[$i]->tiempoEstimado = "0 minutos";
        }
        
        $data[$i]->turnoActual = 0;
        $data[$i]->TipoTurno = 1;
        $data[$i]->cliente = '';
        if(count($dataTurnoCliente) > 0){
            $data[$i]->turnoActual = $dataTurnoCliente[0]->turnoActual;
            $data[$i]->TipoTurno = $dataTurnoCliente[0]->tipoTurno;
            $data[$i]->cliente = $dataTurnoCliente[0]->cliente;
        }
        $data[$i]->numeroTurno = $dataNumeroTurno[0]->numeroTurno;
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
      $idServicio= $request->getAttribute("idServicio");
      $query = "SELECT DISTINCT "
              . "emp.id as idEmpleado,"
              . "ser.id as idServicio,"
              . "CONCAT(emp.nombres, ' ', emp.apellidos) AS empleado,"
              . "ser.nombre as servicio, "
              . "'' as tiempoEstimado, "
              . "'' as cliente, "
              . "'' as turnoActual, "
              . "su.nombre as sucursal, "
              . "em.razonSocial as Empresa, "
              . "su.id as idSucursal "
              . "FROM "
              . "empleado emp "
              . "INNER JOIN "
              . "serviciosempleado seremp "
              . "ON(seremp.idEmpleado = emp.id) "
              . "INNER JOIN servicio ser "
              . "ON(ser.id = seremp.idServicio) "
              . "INNER JOIN sucursal su "
              . "ON(su.id = emp.idSucursal) "
              . "INNER JOIN empresa em "
              . "ON(em.id = su.idEmpresa) "
              . "WHERE emp.id = $idEmpleado AND "
              . "ser.id = $idServicio AND "
              . "emp.estadoOnline = 'ACTIVO' AND "
              . "ser.estado = 'ACTIVO'";
      $data = DB::select(DB::raw($query));
      for($i = 0; $i < count($data); $i++){
        //CALCULAR TIEMPO
        $query = "SELECT "
                ."((COALESCE(AVG(TIMESTAMPDIFF(SECOND,fechaInicio,fechaFinal)),0) * turnosFaltantes.faltantes)) as tiempoEstimado, "
                . "turnosFaltantes.faltantes as turnFaltantes "
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
                  ."turno "
                  ."WHERE idEmpleado = ".$data[$i]->idEmpleado." AND "
                  ."idServicio = ".$data[$i]->idServicio." AND "
                  ."estadoTurno = 'TERMINADO' "
                  ."LIMIT 1";
          $dataTiempo = DB::select(DB::raw($query));
          $query = "SELECT "
                    ."tu.turno as turnoActual, "
                    ."CONCAT(cl.nombres,' ',cl.apellidos) as cliente "
                    ."FROM turno as tu "
                    ."INNER JOIN "
                    ."cliente as cl "
                    ."ON(cl.id = tu.idCliente) "
                    ."WHERE tu.idEmpleado = ".$data[$i]->idEmpleado." AND "
                    ."tu.idServicio = ".$data[$i]->idServicio." AND "
                    ."(tu.estadoTurno = 'ATENDIENDO' OR "
                    ."tu.estadoTurno = 'CONFIRMADO' OR tu.estadoTurno = 'SOLICITADO') "
                    . "ORDER BY "
                    . "tu.fechaSolicitud DESC "
                    . "LIMIT 1";
        $dataCliente = DB::select(DB::raw($query));
        if(count($dataTiempo) > 0){
            $val = ceil(($dataTiempo[0]->tiempoEstimado / 60));
            $str = " minuto";
            if($val == 0){
                //5 min
                $val = ceil(5 * $dataTiempo[0]->turnFaltantes);
            }
            if($val != 1){
                $str .= "s";
            }
            $data[$i]->tiempoEstimado = strval($val).$str;
        }else{
            $data[$i]->tiempoEstimado = "0 minutos";
        }

        $data[$i]->turnoActual = 0;
        $data[$i]->cliente = "";
        if(count($dataCliente) > 0){
            $data[$i]->turnoActual = $dataCliente[0]->turnoActual;
            $data[$i]->cliente = $dataCliente[0]->cliente;
        }

      }
      $response->getBody()->write(json_encode($data));
      return $response;
    }

    function fotoperfil(Request $request, Response $response){
      try {
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        $id = $request->getAttribute("id");
        $empleado = Empleado::select("*")
                            ->where("id","=",$id)
                            ->first();
        $empleado->logo     =   $data['logo'];
        $empleado->save();
        $respuesta = json_encode(array('msg' => "Foto modificado correctamente", "std" => 1));
        $response = $response->withStatus(200);
      } catch (Exception $err) {
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
    }
    
    function reporteempleado(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $idsucursal = $request->getAttribute("idsucursal");
        $fechainicial = $request->getAttribute("fechainicial");
        $fechafinal = $request->getAttribute("fechafinal");
        $empl = Empleado::select('id','nombres','apellidos')
                ->where('idSucursal','=',$idsucursal)
                ->get();
        for($i=0;$i<count($empl);$i++){
            /*$suma = Ingreso::select('id')
                        ->where('idEmpleado','=',$empl[$i]->id)
                        ->sum('valor');*/
            $tur = Turno::select('turno.id')
                        ->where('turno.estadoTurno','=','TERMINADO')
                        ->where('turno.idEmpleado','=',$empl[$i]->id)
                        ->whereBetween('turno.fechaSolicitud',array($fechainicial,$fechafinal))
                        ->count();
            $empl[$i]['contador'] = $tur;
        }
        $response->getBody()->write($empl);
        return $response;
    }
    
    function encontrarempleado(Request $request, Response $response){
        $response = $response->withHeader('Content-type', 'application/json');
        $peticion = $request->getAttribute("peticion");
        $data = Empleado::select('nombres','apellidos','email','id','identificacion')
                    ->orwhere('identificacion','=',$peticion)
                    ->orwhere('email','=',$peticion)
                    ->first();
            $respuesta = json_encode(array('empleado' => $data,'std'=>1));
            
            if($data == null){
                $respuesta = json_encode(array('empleado'=>'Humano no encontrado','std'=>0));
            }
            
        $response->getBody()->write($respuesta);
        return $response;
    }

    function enviaremail(Request $request, Response $response){
        try {
            $response = $response->withHeader('Content-type', 'application/json');
            $data = json_decode($request->getBody(),true);
            $id = $data['id'];
            $user = $data['user'];
            $email = $data['email'];
            //$clave = bin2hex($bytes);
            $val = true;
            $para = $email;
            $nombre = $user;
            
            $titulo = utf8_encode('Recuperacion de Clave [Turnomovil]');
            
            $mensaje = ""
                    . "<html>
                        <head>
                          <title>". utf8_encode("Recuperación de Usuario o Contraseña") ."</title>
                        </head>
                        <body>          
                        <img style='height:60px;' src='http://turnomovil.com/images/turnomovil.png' alt=''/>
                          <h1>Hola, $nombre </h1><br/>
                          <h4>Hemos Recibido una solicitud, para recuperar tu Usuario o  Contraseña</h4>

                          <br/>    
                            <h4>Usuario Turnomovil: $nombre</h4>
                          <br/>

                          <h4> Si deseas cambiar tu Contraseña, por favor sigue este enlace para ingresar una Nueva Contraseña</h4>
                          <h4><a href='http://turnomovil.com/sesion.html#/cambiarclave/$id/$email' target='_blank'>Click Aqui, para cambiar la Contraseña</a></h4>
                          <h4>Atentamente</h4>
                          <h4>Turnomovil.com</h4>
                        </body>
                        </html>";
            
            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
       
            $cabeceras .= 'To: '.$nombre.' <'.$para.'>' . "\r\n";
            $cabeceras .= 'From: Turnomovil.com' . "\r\n";
            
            mail($para, $titulo, $mensaje, $cabeceras);
            $respuesta = json_encode(array('msg' => "Enviado correctamente", "std" => 1));
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        $response->getBody()->write($respuesta);
            return $response;
    }
    
    function claveupdate(Request $request, Response $response){
      try {
        $response = $response->withHeader('Content-type', 'application/json');
        $data = json_decode($request->getBody(),true);
        $id = $request->getAttribute("id");
        //$para = $request->getAttribute("email");
        $empleado = Empleado::select("*")
                            ->where("id","=",$id)
                            ->first();
        $empleado->pass     =   sha1($data['pass']);
        $empleado->save();
        
        $respuesta = json_encode(array('msg' => "Clave actualizada correctamente", "std" => 1));
        
        $clave = $data['pass'];
        
        $titulo = utf8_encode('Clave actualizada [Turnomovil]');
        
        $mensaje = ""
                    . "<html>
                        <head>
                          <title>". utf8_encode("Nueva contraseña registrada") ."</title>
                        </head>
                        <body>          
                        <img style='height:60px;' src='http://turnomovil.com/images/turnomovil.png' alt=''/>
                          <h1>Has actualizado tu contraseña </h1><br/>
                          <h4>Gracias por usar nuestros servicios</h4>

                          <br/>    
                            <h4>Tu nueva contraseña es: $clave</h4>
                          <br/>

                          
                        </body>
                        </html>";
        
            $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $cabeceras .= 'To: '.$empleado->nombres.' <'.$empleado->email.'>' . "\r\n"; 
            $cabeceras .= 'From: Turnomovil.com' . "\r\n";
            
            mail($para, $titulo, $mensaje, $cabeceras);
        
        $response = $response->withStatus(200);
      } catch (Exception $err) {
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
    }
    
    function empleadosDisponibles(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $idServicio = $request->getAttribute("idServicio");
    $idSucursal = $request->getAttribute("idSucursal");
    $fecha = $request->getAttribute("fecha");
    $hora = $request->getAttribute("hora");
    $cupos = $request->getAttribute("cupos");
    $dataMitiempo = ServiciosSucursal::select("minutos")
                  ->where("idServicio","=",$idServicio)
                  ->where("idSucursal","=",$idSucursal)
                  ->first();
    $minutos = 60;
    if($data != null){
        $minutos = $dataMitiempo->minutos;
    }

    $empleado = Empleado::select("*")
                  ->where("idSucursal","=",$idSucursal)
                  ->get();

    //$horaFinal = "(sec_to_time(time_to_sec('$hora') + (time_to_sec('$hora') * $cupos)))";
    $tiempo = 0;
    for($i = 0; $i < $cupos; $i++){
      $tiempo += $minutos;
    }
    $horaFinal = "ADDTIME('$hora', SEC_TO_TIME($tiempo*60))";
    $data = array();
    for($i = 0; $i < count($empleado); $i++){
        $query = "SELECT "
                . "tur.id "
                . "FROM "
                . "turno tur "
                . "WHERE "
                . "tur.idServicio = $idServicio AND "
                . "tur.idSucursal = $idSucursal AND "
                . "tur.idEmpleado = ".$empleado[$i]->id." AND "
                . "tur.fechaReserva = '$fecha' AND "
                . "tur.reserva = 'A' AND "
                . "(tur.estadoTurno <> 'TERMINADO' AND tur.estadoTurno <> 'CANCELADO') AND ("
                . "(TIMESTAMP('$fecha','$hora') >= tur.horaReserva AND TIMESTAMP('$fecha','$hora') < tur.horaFinalReserva) OR "
                . "(TIMESTAMP('$fecha','$hora') < tur.horaReserva AND TIMESTAMP('$fecha',$horaFinal) > tur.horaReserva))";
                //. "(TIMESTAMP('$fecha',$horaFinal) > tur.horaReserva AND TIMESTAMP('$fecha',$horaFinal) <= tur.horaFinalReserva))";
        //echo $query;
        $disp = DB::select(DB::raw($query));
        if(count($disp) == 0){
          $data[] = $empleado[$i];
        }
    }

    $response->getBody()->write(json_encode($data));
    return $response;
  }
    
}