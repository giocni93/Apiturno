<?php

$app->get('/empleado/{idEmpleado}/confirmados/turnos', "TurnoControl:getTurnosEnColaByEmpleado");
$app->get('/empleado/{idEmpleado}/solicitados/turnos', "TurnoControl:getTurnosEnEsperaByEmpleado");
$app->put('/turno/{id}', "TurnoControl:cambiarEstadoTurno");
$app->post('/turno', "TurnoControl:postTurno");
$app->post('/anonimo/turno', "TurnoControl:postTurnoAnonimo");
