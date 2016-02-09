<?php
	use Illuminate\Database\Eloquent\Model;

	class ServiciosEmpleado extends Model{
	    protected $table = "serviciosempleado";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
