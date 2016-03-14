<?php
use Slim\Http\Request;
use Slim\Http\Response;

class ClienteControl{

  function getAll(Request $request, Response $response) {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = Cliente::all();
    if(count($data) == 0){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }

  function getById(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("id");
    $data = Cliente::select("*")
                    ->where("id","=",$id)
                    ->first();
    $response->getBody()->write($data);
    return $response;
  }
  
  function getClienteByemail(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $email = $request->getAttribute("email");
    $data = Cliente::select("nombres","apellidos")
                    ->where("email","=",$email)
                    ->first();
    if($data == null){
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($data);
    return $response;
  }
  
  function verificarLoginFacebook(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $id = $request->getAttribute("idFace");
    $cliente = Cliente::select("*")
                    ->where("idFace","=",$id)
                    ->where("estado","=","ACTIVO")
                    ->first();
    $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
    if($cliente == null){
      $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Email o contraseña no validos."));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }
  
  function loginFacebook(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    $cliente = Cliente::select("*")
                    ->where("idFace","=",$data['idFace'])
                    ->where("estado","=","ACTIVO")
                    ->first();
    if($cliente == null){
        try{
            $cliente = new Cliente;
            $cliente->email     =   null;
            $cliente->nombres   =   $data['nombres'];
            $cliente->apellidos =   $data['apellidos'];
            $cliente->telefono  =   "";
            $cliente->pass      =   "";
            $cliente->idPush    =   "01";//$data['idPush'];
            $cliente->idFace    =   $data['idFace'];//$data['idFace'];
            $cliente->estado    =   "ACTIVO";
            $cliente->save();
            $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
            $response = $response->withStatus(200);
        }catch(Exception $err){
            $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
            $response = $response->withStatus(404);
        }
    }
    $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
    $response->getBody()->write($respuesta);
    return $response;
  }

  function post(Request $request, Response $response){
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    //echo $data["email"];
    try{
        $cliente = new Cliente;
        $cliente->email     =   $data['email'];
        $cliente->nombres   =   $data['nombres'];
        $cliente->apellidos =   $data['apellidos'];
        $cliente->telefono  =   $data['telefono'];
        $cliente->pass      =   sha1($data['pass']);
        $cliente->idPush    =   "01";//$data['idPush'];
        $cliente->idFace    =   "01";//$data['idFace'];
        $cliente->estado    =   "ACTIVO";
        $cliente->save();
        $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
        $response = $response->withStatus(200);
    }catch(Exception $err){
        $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
        $response = $response->withStatus(404);
        //echo $respuesta;
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  public function delete(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                      ->where("id","=",$id)
                      ->first();
      $cliente->estado = "INACTIVO";
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Eliminado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;

  }

  public function put(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                          ->where("id","=",$id)
                          ->first();
      $cliente->email     =   $data['email'];
      $cliente->nombres   =   $data['nombres'];
      $cliente->apellidos =   $data['apellidos'];
      $cliente->telefono  =   $data['telefono'];
      $cliente->pass      =   sha1($data['pass']);
      $cliente->idPush    =   "01";//$data['idPush'];
      $cliente->idFace    =   "01";//$data['idFace'];
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }
  
  public function putIdpush(Request $request, Response $response)
  {
    try {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);
      $id = $request->getAttribute("id");
      $cliente = Cliente::select("*")
                          ->where("id","=",$id)
                          ->first();
      $cliente->idPush    =   $data['idPush'];
      $cliente->save();
      $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1));
      $response = $response->withStatus(200);
    } catch (Exception $err) {
      $respuesta = json_encode(array('msg' => "error", "std" => 0,"err" => $err->getMessage()));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;
  }

  function login(Request $request, Response $response)
  {
    $response = $response->withHeader('Content-type', 'application/json');
    $data = json_decode($request->getBody(),true);
    //echo $data["email"];
    $cliente = Cliente::select("*")
                        ->where("email","=",$data["email"])
                        ->where("pass","=",sha1($data["pass"]))
                        ->where("estado","=","ACTIVO")
                        ->first();
    $respuesta = json_encode(array("std" => 1, "cliente" => $cliente, "msg" => "Ok"));
    if($cliente == null){
      $respuesta = json_encode(array('cliente' => null, "std" => 0, "msg" => "Email o contraseña no validos."));
      $response = $response->withStatus(404);
    }
    $response->getBody()->write($respuesta);
    return $response;

  }

}
