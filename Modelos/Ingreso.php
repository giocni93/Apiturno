<?php
	use Illuminate\Database\Eloquent\Model;

	class Ingreso extends Model{
	    protected $table = "ingresos";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}