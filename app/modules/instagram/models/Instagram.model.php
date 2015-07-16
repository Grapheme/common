<?php

class Instagram extends BaseModel {

	protected $guarded = array();

	protected $table = 'instagram';

    /*
	public static $rules = array(
		'name' => 'required',
		#'desc' => 'required',
	);
    */

	public function tags() {
		return $this->hasMany('InstagramTags', 'instagram_id', 'instagram_id');
	}


}