<?php

class MarlboroController extends BaseController {

    public static $name = 'marlboro';
    public static $group = 'marlboro';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        Route::group(array('prefix' => 'admin/' . self::$group), function() {

            Route::any('token', array('as' => 'marlboro.token', 'uses' => __CLASS__.'@getYaDiskToken'));
            Route::post('update_token', array('as' => 'marlboro.update_token', 'uses' => __CLASS__.'@updateToken'));

            Route::any('read', array('as' => 'marlboro.read', 'uses' => __CLASS__.'@readYaDisk'));

            Route::any('get_info', array('as' => 'marlboro.get_info', 'uses' => __CLASS__.'@getInfo'));

            #Route::any('faces/change_status', array('as' => 'eve.change_status', 'uses' => __CLASS__.'@changeStatus'));
            #Route::any('faces/full_delete', array('as' => 'eve.full_delete', 'uses' => __CLASS__.'@fullDelete'));
        });

        #Route::any(self::$name . '/load_photo', array('as' => 'eve.load_photo', 'uses' => __CLASS__.'@postLoadPhoto'));
    }


    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        return array(
            'view'         => 'Просмотр',
            'update_token' => 'Обновление токена',
            'read_disk'    => 'Доступ на чтение Я.Диска',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
            'name' => self::$name,
            'group' => self::$group,
            'title' => 'Marlboro - YaDisk',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {

        $menu = [];

        if (Allow::action(self::$group, 'update_token', true, false)) {

            $menu[] = array(
                'title' => 'Marlboro - YaDisk Token',
                'link' => self::$group . '/' . 'token',
                'class' => 'fa-list-alt',
                'permit' => 'view',
            );
        }

        if (Allow::action(self::$group, 'read_disk', true, false)) {

            $menu[] = array(
                'title' => 'Marlboro - YaDisk Read',
                'link' => self::$group . '/' . 'read?file=digital.sql.gz',
                'class' => 'fa-list-alt',
                'permit' => 'view',
            );
        }

        return $menu;
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

        $this->token = Setting::where('module', self::$group)->where('name', 'ya_disk_token')->pluck('value');

        View::share('module', $this->module);
        View::share('CLASS', __CLASS__);
    }


    public function getYaDiskToken() {

        Allow::permission(self::$group, 'update_token');

        $token = $this->token;

        return View::make($this->module['gtpl'].'yadisk_token', compact('faces', 'token'));
    }


    public function getInfo() {

        $json_response = ['status' => false];

        $input = [
            'user_id' => Input::get('id'),
            'city' => Input::get('city-chose'),
            'yad_name' => Input::get('yad_name'),
        ];

        if ($input['user_id'] && $input['city'] && $input['yad_name']) {

            YaDiskVideo::create($input);
            $json_response['status'] = true;
        }

        return Response::json($json_response);
    }



    public function updateToken() {

        Allow::permission(self::$group, 'update_token');

        $code = Input::get('code');
        #Helper::tad($code);

        $postfields = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => Config::get('site.marlboro.app_id'),
            'client_secret' => Config::get('site.marlboro.app_pass'),
        ];

        $out = '';
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://oauth.yandex.ru/token');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
            $out = curl_exec($curl);
            #echo $out;
            curl_close($curl);
        }

        $out = json_decode($out, true);
        #print_r($out);

        if (isset($out['access_token']) && $out['access_token'] != '') {

            Setting::firstOrCreate([
                'module' => self::$group,
                'name'   => 'ya_disk_token',
                'value'  => $out['access_token'],
            ]);
            echo "Token updated! Close this window & reload page with token.";
            echo "<script>
window.opener.location.reload(false);
window.close();
</script>";

        } else {

            echo "Error:";
            print_r($out);
        }

        #return Redirect::route('marlboro.token');
        #return Response::json($json_response, 200);
    }


    public function readYaDisk() {

        Allow::permission(self::$group, 'read_disk');

        ## Имя файла, которое будем искать
        #$filename = 'digital.sql.gz';
        $filename = Input::get('file');

        ## Опции запроса
        $postfields = [
            'path' => '/backups',
            'sort' => 'created',
            'limit' => '100',
            'offset' => '0',
        ];

        ## Получаем список файлов в папке на Я.Диске
        $out = yadisk_request($this->token, '/resources', $postfields);

        ## Ищем нужный файл по имени среди всех переданных значений
        $file = null;
        $files = @$out['_embedded']['items'];
        #$files = @$out['items'];
        if (isset($files) && is_array($files) && count($files)) {
            foreach ($files as $file) {
                if ($file['name'] == $filename) {
                    break;
                }
            }
        }

        ## Если файл найден...
        if (is_array($file)) {

            Helper::ta($file);

            ## Генерим прямую ссылку на файл
            $result = yadisk_request($this->token, '/resources/download', ['path' => $file['path']]);
            Helper::ta($result);

            #$link = @$result['href'];

            ##
            ## Здесь нужно будет обновить ссылку на файл для записи (видимо по id)
            ##
        }
    }
}


if (!function_exists('yadisk_request')) {

    function yadisk_request($token, $method, $attrs = null) {

        $line = Helper::arrayToUrlAttributes($attrs);
        #echo $line;

        $out = '';
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://cloud-api.yandex.net/v1/disk' . $method . '?' . $line);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: ' . $token]);
            $out = curl_exec($curl);
            #echo $out;
            curl_close($curl);
        }

        $out = json_decode($out, true);
        #Helper::ta($out);

        return $out;
    }
}