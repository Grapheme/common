<?php

class EveController extends BaseController {

    public static $name = 'eve';
    public static $group = 'eve';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        Route::group(array('prefix' => 'admin/' . self::$name), function() {

            Route::any('faces', array('as' => 'eve.faces', 'uses' => __CLASS__.'@getEveFaces'));
            Route::any('faces/change_status', array('as' => 'eve.change_status', 'uses' => __CLASS__.'@changeStatus'));
        });

        Route::any(self::$name . '/load_photo', array('as' => 'eve.load_photo', 'uses' => __CLASS__.'@postLoadPhoto'));
    }


    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        return array(
            'view'         => 'Просмотр',
            'moderate'     => 'Доступ к модерированию',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
            'name' => self::$name,
            'group' => self::$group,
            'title' => 'Eve - лица',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
                'title' => 'Eve - лица',
                'link' => self::$group . '/' . 'faces',
                'class' => 'fa-list-alt',
                'permit' => 'view',
            ),
        );
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
            $faces = $faces->where('status', '=', (string)$filter_status);

        if ($order_by && in_array($order_by, $order_bys)) {

            if (!in_array($order_type, $order_types))
                $order_type = 'ASC';

            $faces = $faces->orderBy($order_by, $order_type);
        }

        $faces = $faces->paginate($pagination_limit);

        return View::make($this->module['gtpl'].'eve-faces', compact('faces'));
    }


    public function postLoadPhoto() {

        cors();

        if (Input::get('debug') == 1)
            Helper::tad(Input::all());

        $json_response = ['status' => false];

        $app_path = public_path('uploads/eve');

        $city = Input::get('city');
        $data = Input::get('data');
        $image = NULL;

        if (Input::hasFile('image')) {

            $image = Input::file('image');
            $destinationPath = $app_path;
            $fileName = sha1($image->getClientOriginalName() . '_' . microtime()) . '.jpg';
            $image->move($destinationPath, $fileName);
            $image = $fileName;
        }

        if (
            ($city !== NULL && $data !== NULL && $image !== NULL)
            || !trim($data)
            || !trim($image)
        ) {

            EveFace::create([
                'city' => $city,
                'data' => $data,
                'image' => $image,
            ]);
            $json_response['status'] = true;
        }

        return Response::json($json_response, 200);
    }


    public function changeStatus() {

        $json_response = ['status' => false];
        $json_response['hide'] = false;

        #Helper::ta(Input::all());
        $id = (int)Input::get('id');
        $status = (int)Input::get('status');

        $statuses = [1, 2, 3];

        if (in_array($status, $statuses)) {

            $face = new EveFace();
            $face = $face->where('id', $id)->first();
            if ($face->status != $status) {

                $face->update(['status' => $status]);
                $json_response['hide'] = true;
            }
            $face->touch();
            $json_response['status'] = true;
        }

        return Response::json($json_response, 200);
    }
}

