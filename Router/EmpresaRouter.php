<?php

$app->post('/empresa', "EmpresaControl:post");
$app->put('/logoempresa/{id}', "EmpresaControl:putfotoservidor");
$app->get('/empresa/{id}',"EmpresaControl:getId");
$app->get('/empresa',"EmpresaControl:getAll");
$app->get('/empresas/maxid',"EmpresaControl:maxId");