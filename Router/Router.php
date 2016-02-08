<?php

  //INICIO LOGIN
  $app->post('/login', "UsuarioControl:login");
  //FIN LOGIN

  // INICIO CLIENTE
    $app->get('/cliente', "ClienteControl:getAllClientes");
    $app->get('/cliente/{email}', "ClienteControl:getClientes");
    $app->post('/cliente', "ClienteControl:postClientes");
    $app->put('/cliente/{email}', "ClienteControl:putClientes");
    $app->put('/cliente/{email}/foto', "ClienteControl:putFoto");
    $app->put('/cliente/{email}/idpush', "ClienteControl:putIdPushByCliente");
  //FIN CLIENTE

  // INICIO EMPRESA
    $app->post('/empresa', "EmpresaControl:postEmpresas");
    $app->post('/empresa/{email}/calificacion', "EmpresaControl:postCalificacion");
    $app->put('/empresa/{email}/foto', "EmpresaControl:putFoto");
  //FIN EMPRESA

  // INICIO SUCURSAL
    $app->get('/sucursales', "SucursalControl:getAllSucursales");
    $app->get('/empresa/{email}/sucursales', "SucursalControl:getSucursalesByEmpresa");
    $app->post('/sucursales', "SucursalControl:postSucursales");
  //FIN SUCURSAL

  // INICIO USUARIO
    $app->post('/usuario', "UsuarioControl:postUsuarios");
    $app->put('/idpush/usuario/{id}', "UsuarioControl:putIdPushByUsuario");
  //FIN USUARIO

  // INICIO CATEGORIA
    $app->post('/cliente/{email}/categorias', "CategoriaControl:postCategoriasCliente");
    $app->post('/sucursal/{id}/categorias', "CategoriaControl:postCategoriasSucursal");
    $app->get('/categorias', "CategoriaControl:getAllCategorias");
    $app->get('/cliente/{email}/categorias', "CategoriaControl:getCategoriasByCliente");
    $app->get('/sucursal/{id}/categorias', "CategoriaControl:getCategoriasBySucursal");
  //FIN CATEGORIA

  // INICIO PETICION
    $app->post('/peticion', "PeticionControl:postPeticion");
    $app->get('/cliente/{email}/peticiones', "PeticionControl:peticionesByCliente");
    $app->get('/categoria/{id}/peticiones', "PeticionControl:peticionesByCategoria");
    $app->post('/peticion/{id}/foto', "PeticionControl:putFoto");
  //FIN PETICION

  // INICIO OFERTA
    $app->post('/oferta', "OfertaControl:postOferta");
    $app->get('/cliente/{email}/peticiones/ofertas', "OfertaControl:OfertasDePeticiones");
    $app->get('/usuario/{id}/ofertas', "OfertaControl:ofertasByUsuario");
    $app->post('/oferta/{id}/foto', "OfertaControl:putFoto");
  //FIN OFERTA

  // INICIO PROMOCION
    $app->post('/promocion', "PromocionControl:postPromocion");
    $app->post('/categorias/promociones', "PromocionControl:promocionesByCategorias");
    $app->get('/usuario/{id}/promociones', "PromocionControl:promocionesByUsuario");
    $app->put('/promocion/{id}/foto', "PromocionControl:putFoto");
  //FIN PROMOCION
