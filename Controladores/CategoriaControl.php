<?php
use Slim\Http\Request;
use Slim\Http\Response;

class CategoriaControl{

  function getAllCategorias(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      $data = Categoria::all();
      if(count($data) == 0){
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($data);
      return $response;
  }

  function getCategoriasByCliente(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      $email = $request->getAttribute('email');
      $data = Categoria::select("categoria.*")
                        ->join("categoriascliente","categoria.id","=","categoriascliente.idCategoria")
                        ->where("categoriascliente.idCliente","=",$email)
                        ->get();
      if(count($data) == 0){
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($data);
      return $response;
  }

  function getCategoriasBySucursal(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute('id');
      $data = Categoria::select("categoria.*")
                        ->join("categoriassucursal","categoria.id","=","categoriassucursal.idCategoria")
                        ->where("categoriassucursal.idSucursal","=",$id)
                        ->get();
      if(count($data) == 0){
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($data);
      return $response;
  }

  function postCategoriasCliente(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      $email = $request->getAttribute('email');
      $data = json_decode($request->getBody(),true);
      try{
          $cc = CategoriasCliente::where('idCliente', '=', $email)->first();
          $cc->delete();
          for ($i=0; $i < count($data['categorias']); $i++) {
              $cc = new CategoriasCliente;
              $cc->idCategoria     =   $data['categorias'][$i];
              $cc->idCliente       =   $email;
              $cc->save();
          }
          $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, "obj" => $data));
          $response = $response->withStatus(200);
      }catch(Exception $err){
          $respuesta = json_encode(array('msg' => "La empresa ya existe", "std" => 0, "err" => $err->getMessage()));
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
  }

  function postCategoriasSucursal(Request $request, Response $response) {
      $response = $response->withHeader('Content-type', 'application/json');
      $id = $request->getAttribute('id');
      $data = json_decode($request->getBody(),true);
      try{
          $cc = CategoriasSucursal::where('id', '=', $id)->first();
          $cc->delete();
          for ($i=0; $i < count($data['categorias']); $i++) {
              $cc = new CategoriasSucursal;
              $cc->idCategoria     =   $data['categorias'][$i];
              $cc->idSucursal      =   $id;
              $cc->save();
          }
          $respuesta = json_encode(array('msg' => "Guardado correctamente", "std" => 1, "obj" => $data));
          $response = $response->withStatus(200);
      }catch(Exception $err){
          $respuesta = json_encode(array('msg' => "La empresa ya existe", "std" => 0, "err" => $err->getMessage()));
          $response = $response->withStatus(404);
      }
      $response->getBody()->write($respuesta);
      return $response;
  }
}
