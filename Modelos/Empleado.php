<?php
	use Illuminate\Database\Eloquent\Model;

	class Empleado extends Model{
	    protected $table = "empleado";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
