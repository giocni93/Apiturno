<?php
$app->get('/empleado', "EmpleadoControl:getAll");
$app->get('/empleado/{id}', "EmpleadoControl:getById");
$app->post('/empleado', "EmpleadoControl:post");
$app->delete('/empleado/{id}', "EmpleadoControl:delete");
$app->put('/empleado/{id}',"EmpleadoControl;update");
$app->post('/empleado/sesion', "EmpleadoControl:sesion");
$app->put('/empleado/{id}/idpush', "EmpleadoControl:updatePush");
