<?php

class EveController extends BaseController {

    public static $name = 'eve';
    public static $group = 'application';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        Route::group(array('prefix' => self::$name), function() {

            Route::any('load_photo', array('as' => 'eve.load_photo', 'uses' => __CLASS__.'@postLoadPhoto'));
            Route::any('faces', array('as' => 'eve.faces', 'uses' => __CLASS__.'@getEveFaces'));
        });
    }


    /****************************************************************************/


	public function __construct(){

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'rest' => self::$group,
            'tpl' => static::returnTpl('admin'),
            'gtpl' => static::returnTpl(),

            #'entity' => self::$entity,
            #'entity_name' => self::$entity_name,
        );

        View::share('module', $this->module);
        View::share('CLASS', __CLASS__);
    }


    public function getEveFaces() {

        $pagination_limit = 10;
        $order_by = Input::get('order_by');
        $order_type = Input::get('order_type');
        $filter_city = Input::get('filter_city');
        $filter_status = Input::get('filter_status');

        $order_bys = ['created_at', 'status'];
        $order_types = ['ASC', 'DESC'];

        $faces = (new EveFace);

        if ($filter_city)
            $faces = $faces->where('city', $filter_city);

        if ($filter_status)
            $faces = $faces->where('status', $filter_status);

        if ($order_by && in_array($order_by, $order_bys)) {

            if (!is_array($order_type, $order_types))
                $order_type = 'ASC';

            $faces = $faces->orderBy($order_by, $order_type);
        }

        $faces = $faces->paginate($pagination_limit);

        return View::make($this->module['gtpl'].'eve-faces', compact('faces'));
    }


    public function postLoadPhoto() {

        cors();

        Helper::tad(Input::all());

        $app_path = public_path('uploads/eve');

        $city = Input::get('city');
        $data = Input::get('data');
        $image = Input::get('image');

        /*
        if (Input::get('image')) {

            $tmp = explode(',', Input::get('image'));
            $img_data = base64_decode($tmp[1]);
            $file = $app_path . '/' . sha1($img_data) . '.png';
            file_put_contents($file, $img_data);
        }
        */

    }
}

