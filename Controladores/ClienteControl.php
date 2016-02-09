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
                      ->get();
      $response->getBody()->write($data);
      return $response;
  }

  function post(Request $request, Response $response){
      $response = $response->withHeader('Content-type', 'application/json');
      $data = json_decode($request->getBody(),true);

      try{
          $cliente = new Cliente;
          $cliente->email     =   $data['email'];
          $cliente->nombres   =   $data['nombres'];
          $cliente->apellidos =   $data['apellidos'];
          $cliente->telefono  =   $data['telefono'];
          $cliente->pass      =   sha1($data['pass']);
          $cliente->idPush    =   $data['idPush'];
          $cliente->idFace    =   $data['idFace'];
          $cliente->estado    =   "ACTIVO";
          $cliente->save();
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
