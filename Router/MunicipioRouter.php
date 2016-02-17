<?php

$app->get('/municipios',"MunicipiosControl:getAll");
$app->get('/municipios/iddepartamento/{id}',"MunicipiosControl:getMunicipioId");