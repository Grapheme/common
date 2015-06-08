<?php

class EveFace extends BaseModel {

    protected $guarded = array();

    protected $table = 'eve_faces';

    ## http://laravel.ru/articles/odd_bod/your-first-model
    protected $fillable = array(
        'status',
        'city',
        'data',
        'image',
        'settings'
    );

}