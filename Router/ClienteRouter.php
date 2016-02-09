<?php

$app->get('/cliente', "ClienteControl:getAll");
$app->get('/cliente/{id}', "ClienteControl:getById");
$app->post('/cliente', "ClienteControl:post");
$app->delete('/cliente/{id}', "ClienteControl:delete");
$app->put('/cliente/{id}',"ClienteControl;update");
