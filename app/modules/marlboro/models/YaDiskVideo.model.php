<?php

class YaDiskVideo extends BaseModel {

    protected $guarded = array();

    protected $table = 'marlboro_yadisk';

    ## http://laravel.ru/articles/odd_bod/your-first-model
    protected $fillable = array(
        'user_id',
        'city',
        'yad_name',
        'yad_link',

        'firstname',
        'lastname',
        'patronymic',
    );

}