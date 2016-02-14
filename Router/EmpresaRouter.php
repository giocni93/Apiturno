<?php

$app->post('/empresa', "EmpresaControl:post");
$app->post('/empresa/logo',"EmpresaControl:postfoto");
$app->get('/empresa/{id}',"EmpresaControl:getId");
$app->get('/empresa',"EmpresaControl:getAll");
$app->get('/empresas/maxid',"EmpresaControl:maxId");