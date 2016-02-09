<?php
	use Illuminate\Database\Eloquent\Model;

	class Cliente extends Model{
	    protected $table = "cliente";
			protected $primaryKey = "id";
	    public $timestamps = false;
	}
