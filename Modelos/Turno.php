<?php
	use Illuminate\Database\Eloquent\Model;

	class Turno extends Model{
	    protected $table = "turno";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
