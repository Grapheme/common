<?php

class InstagramTags extends BaseModel {

	protected $guarded = array();

	public $table = 'instagram_tags';

    public $timestamps = false;

    /*
	public static $rules = array(
		'name' => 'required',
		#'desc' => 'required',
	);
    */

	public function photo() {

        return $this->hasOne('Instagram', 'instagram_id', 'instagram_id');
	}

}