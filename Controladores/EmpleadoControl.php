<?php
use Slim\Http\Request;
use Slim\Http\Response;

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
    $data = Empleado::select("*")
                    ->where("id","=",$id)
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
    $data = Empleado::select("id","nombres","apellidos","email")
                    ->where("pass","=",sha1($data['pass']))
                    ->where("identificacion","=",$data['identificacion'])
                    ->first();
    if($data == null){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
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


}
