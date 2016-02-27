<?php
$app->get('/empleado', "EmpleadoControl:getAll");
$app->get('/empleado/{id}', "EmpleadoControl:getById");
$app->post('/empleado', "EmpleadoControl:post");
$app->delete('/empleado/{id}', "EmpleadoControl:delete");
$app->put('/empleado/{id}',"EmpleadoControl:update");
$app->post('/empleado/sesion', "EmpleadoControl:sesion");
$app->put('/empleado/{id}/idpush', "EmpleadoControl:updatePush");
$app->get('/sucursal/{idSucursal}/empleado', "EmpleadoControl:getEmpleadosBySucursal");
$app->put('/empleado/{id}/pass',"EmpleadoControl:updatePass");
$app->post('/empleado/login', "EmpleadoControl:sesionlogin");
$app->post('/superadmi', "EmpleadoControl:postsuperadmin");
$app->put('/empleado/estado/admin/{id}',"EmpleadoControl:updateadminestado");
$app->put('/empleado/estado/admin/desac/{id}',"EmpleadoControl:updateadminestadodesactivar");
$app->get('/empleado/bysucursal/{id}', "EmpleadoControl:getEmpleadoByIdsucursal");