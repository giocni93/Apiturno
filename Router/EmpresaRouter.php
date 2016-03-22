<?php

$app->post('/empresa', "EmpresaControl:post");
$app->put('/logoempresa/{id}', "EmpresaControl:putfotoservidor");
$app->get('/empresa/{id}',"EmpresaControl:getId");
$app->get('/empresa',"EmpresaControl:getAll");
$app->get('/empresas/maxid',"EmpresaControl:maxId");
$app->put('/empresa/{id}',"EmpresaControl:updateempresa");
$app->put('/empresaestado/{id}',"EmpresaControl:updateempresaestado");
$app->put('/empresaestadodescativar/{id}',"EmpresaControl:updateempresaestadodesactivar");
$app->get('/empresas/activas',"EmpresaControl:empresaactivas");