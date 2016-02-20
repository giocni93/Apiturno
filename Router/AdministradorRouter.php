<?php
$app->post('/administrador',"AdministradorControl:post");
$app->post('/administrador/empresa',"AdministradorControl:postadminempresa");
$app->post('/administradorsesion',"AdministradorControl:sesion");
$app->put('/admin/estado/admin/{id}',"AdministradorControl:updateadminestado");
$app->put('/admin/estado/admin/desac/{id}',"AdministradorControl:updateadminestadodesactivar");