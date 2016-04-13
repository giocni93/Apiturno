<?php

$app->get('/cliente', "ClienteControl:getAll");
$app->get('/cliente/{id}', "ClienteControl:getById");
$app->post('/cliente', "ClienteControl:post");
$app->delete('/cliente/{id}', "ClienteControl:delete");
$app->put('/cliente/{id}',"ClienteControl:put");
$app->post('/cliente/sesion',"ClienteControl:login" );
$app->get('/email/{email}/cliente', "ClienteControl:getClienteByemail");
$app->put('/idpush/cliente/{id}',"ClienteControl:putIdpush");
$app->post('/cliente/sesion/facebook',"ClienteControl:loginFacebook" );
$app->get('/facebook/{idFace}/cliente', "ClienteControl:verificarLoginFacebook");
$app->put('/cliente/perfil/{id}',"ClienteControl:putperfilcliente");
$app->post('/cliente/register',"ClienteControl:postcliente");
$app->get('/idmax/cliente',"ClienteControl:maxId");
$app->put('/cliente/{id}/contrasena',"ClienteControl:putContrasenaCliente");
$app->get('/ver/cliente/{id}',"ClienteControl:vercliente");
$app->post('/cliente/email',"ClienteControl:enviaremail");
