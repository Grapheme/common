<?php

class YaDiskVideo extends BaseModel {

    protected $guarded = array();

    protected $table = 'ya_disk_video';

    ## http://laravel.ru/articles/odd_bod/your-first-model
    protected $fillable = array(
        'status',
        'city',
        'data',
        'image',
        'settings'
    );

}