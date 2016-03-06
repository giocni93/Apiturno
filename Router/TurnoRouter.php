<?php

$app->get('/empleado/{idEmpleado}/servicio/{idServicio}/confirmados/turnos', "TurnoControl:getTurnosEnColaByEmpleado");
$app->get('/empleado/{idEmpleado}/servicio/{idServicio}/solicitados/turnos', "TurnoControl:getTurnosEnEsperaByEmpleado");
$app->put('/servicio/{idServicio}/empleado/{idEmpleado}/turno/{id}', "TurnoControl:cambiarEstadoTurno");
$app->post('/turno', "TurnoControl:postTurno");
$app->get('/cliente/{idCliente}/turnos', "TurnoControl:getTurnosCliente");

$app->post('/anonimo/turno', "TurnoControl:postTurnoAnonimo");
$app->get('/turno/pedidos/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosxestado");
$app->get('/turno/pedidos/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnoxservicio");
$app->get('/turno/empresa/servicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresa");
$app->get('/turno/empresaxservicio/{id}/{fechainicial}/{fechafinal}',"TurnoControl:turnosempresaxservicios");
$app->get('/getbysucursal/{id}',"TurnoControl:getsucursalid");
$app->get('/reporte/empleado/{id}/{fechainicial}/{fechafinal}',"TurnoControl:empleadomasturnos");
