<?php
	use Illuminate\Database\Eloquent\Model;

	class Ejemplo extends Model{
	    protected $table = "ejemplo";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
