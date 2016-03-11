<?php

$app->get('/cliente', "ClienteControl:getAll");
$app->get('/cliente/{id}', "ClienteControl:getById");
$app->post('/cliente', "ClienteControl:post");
$app->delete('/cliente/{id}', "ClienteControl:delete");
$app->put('/cliente/{id}',"ClienteControl:put");
$app->post('/cliente/sesion',"ClienteControl:login" );
$app->get('/email/{email}/cliente', "ClienteControl:getClienteByemail");
$app->put('/idpush/cliente/{id}',"ClienteControl:putIdpush");