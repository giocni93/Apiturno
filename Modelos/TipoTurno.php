<?php
	use Illuminate\Database\Eloquent\Model;

	class TipoTurno extends Model{
	    protected $table = "tipoturno";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
