<?php 
$app->get('/ingreso',"IngresoControl:getingreso");
$app->get('/ingreso/empresa/{idempresa}/{fechainicial}/{fechafinal}',"IngresoControl:contablidadempresa");