<?php

$app->get('/empleado/{idEmpleado}/confirmados/turnos', "TurnoControl:getTurnosEnColaByEmpleado");
$app->get('/empleado/{idEmpleado}/solicitados/turnos', "TurnoControl:getTurnosEnEsperaByEmpleado");
$app->put('/turno/{id}', "TurnoControl:cambiarEstadoTurno");
$app->post('/turno', "TurnoControl:postTurno");
$app->get('/turno/pedidos/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosxestado");
$app->get('/turno/pedidos/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnoxservicio");
$app->get('/turno/empresa/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresa");
$app->get('/turno/empresaxservicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresaxservicios");
$app->get('/getbysucursal/{id}',"TurnoControl:getsucursalid");
