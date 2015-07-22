<?php

class MarlboroController extends BaseController {

    public static $name = 'marlboro';
    public static $group = 'marlboro';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        Route::group(array('prefix' => 'admin/' . self::$group), function() {

            Route::any('token', array('as' => 'marlboro.token', 'uses' => __CLASS__.'@getYaDiskToken'));
            Route::any('update_token', array('as' => 'marlboro.update_token', 'uses' => __CLASS__.'@updateToken'));

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
                'title' => 'Marlboro - видео',
                'link' => self::$group . '/' . 'token',
                'class' => 'fa-list-alt',
                'permit' => 'view',
            );
        }

        /*
        if (Allow::action(self::$group, 'read_disk', true, false)) {

            $menu[] = array(
                'title' => 'Marlboro - YaDisk Read',
                'link' => self::$group . '/' . 'read?file=digital.sql.gz',
                'class' => 'fa-list-alt',
                'permit' => 'view',
            );
        }
        */

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
            'city'          => Input::get('city-chose'),

            #'user_id'       => Input::get('id'),
            'firstname'     => Input::get('firstname'),
            'lastname'      => Input::get('lastname'),
            'patronymic'    => Input::get('patronymic'),

            'yad_name'      => Input::get('yad_name'),
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

        $return = '';

        ## Имя файла, которое будем искать
        #$filename = 'digital.sql.gz';
        $filename = Input::get('file');

        /*
        ## Опции запроса
        $postfields = [
            'path' => '/',
            'sort' => 'created',
            'limit' => '100',
            'offset' => '0',
        ];
        ## Получаем список файлов в папке на Я.Диске
        $out = yadisk_request($this->token, '/resources', $postfields);
        #*/

        ## Получаем записи без ссылки на видео
        $records = YaDiskVideo::orderBy('created_at', 'ASC')->where('yad_link', '')->get();

        ## Если есть записи без видео - обработаем их
        if (count($records)) {

            ## Перебираем все записи без ссылки, и формируем массив
            $new_links = [];
            foreach ($records as $record) {
                #Helper::ta($record);
                $new_links[$record->yad_name] = '';
            }
            $new_links_count = count($new_links);

            ## Опции запроса
            $postfields = [
                'limit' => '10000',
                'offset' => '0',
            ];
            ## Получаем плоский список всех файлов
            ## https://tech.yandex.ru/disk/api/reference/all-files-docpage/
            $out = yadisk_request($this->token, '/resources/files', $postfields);
            #Helper::ta($out);

            ## Если есть файлы на Я.Диске
            if (isset($out) && is_array($out) && isset($out['items']) && is_array($out['items']) && count($out['items']) && $new_links_count) {

                ## Перебираем все файлы
                foreach ($out['items'] as $item) {

                    ## Если в БД есть запись (без ссылки) для файла (с Я.Диска) с текущим именем - получим ссылку
                    if (isset($new_links[basename($item['path'])])) {

                        ## Получаем ссылку на скачивание
                        ## https://tech.yandex.ru/disk/api/reference/content-docpage/
                        $result = yadisk_request($this->token, '/resources/download', ['path' => urlencode($item['path'])]);
                        #Helper::ta($result);

                        if (isset($result) && is_array($result) && isset($result['href']) && $result['href'])
                            $new_links[basename($item['path'])] = $result['href'];

                        $new_links_count--;
                        if (!$new_links_count)
                            break;
                    }
                }
            }

            #Helper::tad($new_links);

            ## Перебираем все записи без ссылки, и обновляем ссылку
            foreach ($records as $record) {

                #Helper::ta($record);

                if (isset($new_links[$record->yad_name]) && $new_links[$record->yad_name]) {

                    $record->yad_link = $new_links[$record->yad_name];
                    $record->save();
                }
            }
        }

        ## Получаем записи со ссылкой на видео
        $records = YaDiskVideo::orderBy('created_at', 'ASC')->where('yad_link', '!=', '')->get();
        #Helper::tad($records);

        if (count($records)) {

            $lines = [];

            foreach ($records as $record) {

                #$lines[] = '"' . implode('";"', [$record->user_id, $record->city, $record->yad_name, $record->yad_link]) . '"';

                /*
                'firstname' => Input::get('firstname'),
                'lastname' => Input::get('lastname'),
                'patronymic' => Input::get('patronymic'),
                */

                $lines[] = '"' . implode('";"', [$record->firstname, $record->lastname, $record->patronymic, $record->city, $record->yad_name, $record->yad_link]) . '"';
            }

            $return = implode("\n", $lines);
        }

        return Response::make($return, 200, ['Content-Type' => 'application/csv', 'Content-Disposition' => 'attachment; filename="report_'.date('Y-m-d').'.csv"']);

        /*
        if (Input::get('debug') == 1) {
            Helper::tad($out);
        }
        */

        /*
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
        */
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