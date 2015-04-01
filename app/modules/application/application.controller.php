<?php

class ApplicationController extends BaseController {

    public static $name = 'application';
    public static $group = 'application';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {

        Route::group(array('prefix' => 'app-signature'), function() {

            Route::any('work', array('as' => 'app-signature.work', 'uses' => __CLASS__.'@getAppSignatureWork'));
        });
    }


    /****************************************************************************/


	public function __construct(){
        #
	}


    public function getAppSignatureWork() {

        cors();

        $app_path = public_path('uploads/app-signature');

        $file_bg = $app_path . '/bg.png';
        $file_font = $app_path . '/webfont.ttf';
        $file_sign = $app_path . '/sign.png';
        $remove_sign_file = false;

        $font_path = $file_font;
        $font_size = 24;
        $font_color = '#ffffff';

        ## Способ выравнивания текста
        $align = "left";
        #$align = "center";
        #$align = "right";

        $signature = Input::get('image');
        $text = Input::get('text');
        $email = Input::get('email');

        $av_top_offset = 100; // отступ сверху от края фона до подписи
        $av_bottom_offset = 100; // отступ снизу от подписи

        ## Поправочный коэффициент, непонятно почему его приходится вводить..
        ## Видимо imagettfbbox считает ширину получаемого блока с ошибкой.
        $text_format_coeff = 1.15;
        #$text_format_coeff = 1.0;

        /***************************************************************************************/

        $signature = '';
        $text = '<p>Уникальная деталь Вашего характера – <strong>реалистичный взгляд на окружающий мир</strong>. В подписи также проявляются черты, свойственные людям, способным быстро ориентироваться в ситуации.</p><p>Вы обладаете <strong>стратегическими способностями, а  обстоятельность, усидчивость, настойчивость</strong> позволяют реализовывать многогранные бизнес-проекты.</p><p>Также в профессиональной деятельности <strong>верное решение Вам помогает принять жизненный опыт</strong>.</p><p>В обществе <strong>Вы проявляете повышенные требования к окружающим и избирательность</strong>. Благодаря такому амбициозному подходу, Вы добиваетесь поставленных целей.</p><p>В почерке находит отражение <strong>Ваша способность убеждать</strong>.</p><p><strong>В личной жизни Вы цените постоянство.</strong></p><p><strong>Вы интересный собеседник</strong> и всегда объективно излагаете свою позицию.</p><p>Широкий круг Ваших интересов позволяет быть активным всегда и везде. Вам нравятся светские мероприятия в клубах и вечеринки на открытом воздухе.</p><p>Уникальное сочетание деталей в подписи отражает Вашу индивидуальность.</p>';
        $email = 'az@grapheme.ru';

        /***************************************************************************************/


        if (Input::get('text'))
            $text = Input::get('text');

        if (Input::get('email'))
            $email = Input::get('email');

        if (Input::get('image')) {

            $tmp = explode(',', Input::get('image'));
            $img_data = base64_decode($tmp[1]);
            $file_sign = $app_path . '/' . sha1($img_data) . '.png';
            file_put_contents($file_sign, $img_data);
            $remove_sign_file = true;
        }


        $text = trim(strip_tags(str_replace('</p><p>', "\n\n", $text)));

        /**
         * Создаем подпись
         */
        $sign_img = ImageManipulation::make($file_sign);
        $sign_width = $sign_img->width();
        $sign_height = $sign_img->height();

        /**
         * Карточка - создаем из источника, узнаем размеры
         */
        $img = ImageManipulation::make($file_bg);
        $img_width  = $img->width();
        $img_height = $img->height();
        #Helper::d($img_width . " x " . $img_height);

        /**
         * Вставляем подпись
         */
        $img->insert($sign_img, 'top', 0, $av_top_offset);
        $sign_img->destroy();

        /**
         * Функция-замыкание, для вывода текста
         */
        $img_text_closure = function($font) use ($font_path, $font_size, $font_color) {
            $font->file($font_path);
            $font->size($font_size);
            $font->color($font_color);
            $font->align('center');
            $font->valign('top');
            //$font->angle(45);
        };

        /**
         * Подготавливаем текст - делаем переносы, чтобы текст поместился в отведенные ему рамки
         */
        $width_text = $img_width * $text_format_coeff;
        #$width_text = 1000;

        $arr = explode(' ', $text);
        $ret = '';
        // Перебираем наш массив слов
        foreach($arr as $word) {

            // Временная строка, добавляем в нее слово
            $tmp_string = trim($ret . ' ' . $word);

            // Получение параметров рамки обрамляющей текст, т.е. размер временной строки
            $textbox = imagettfbbox($font_size, 0, $font_path, $tmp_string);

            #if ($debug_text_format)
            #    Helper::d($textbox[2] . ' >= ' . $width_text . ' ? ' . ($textbox[2] >= $width_text ? 'yes' : 'no') . '<br/>' . $tmp_string);

            // Если временная строка не укладывается в нужные нам границы, то делаем перенос строки, иначе добавляем еще одно слово
            if($textbox[2] >= $width_text)
                $ret .= ($ret == "" ? "" : "\n") . $word;
            else
                $ret .= ($ret == "" ? "" : " ") . $word;
        }

        #if ($debug_text_format)
        #    Helper::dd($ret);

        $formatted_promise_text = $ret;


        ## Выравнивание
        if($align=="left") {

            // Разбиваем снова на массив строк уже подготовленный текст
            $arr = explode("\n", $formatted_promise_text);

            // Расчетная высота смещения новой строки
            $height_tmp = $av_top_offset + $sign_height + $av_bottom_offset; # высота аватара + отступ (50px)

            //Выводить будем построчно с нужным смещением относительно левой границы
            foreach($arr as $str) {

                // Накладываем текст на картинку с учетом смещений
                #imagettftext($im, $font_size, 0, 50 + $left_x, 50 + $height_tmp, $black, $font_path, $str); // 50 - это отступы от края
                $img->text($str, abs(($width_text-$img_width)/2), $height_tmp, function($font) use ($font_path, $font_size, $font_color) {
                    $font->file($font_path);
                    $font->size($font_size);
                    $font->color($font_color);
                    $font->align('left');
                    $font->valign('top');
                    //$font->angle(45);
                });

                // Смещение высоты для следующей строки
                $height_tmp = $height_tmp + $font_size * 1.5;
            }

        } else {

            // Разбиваем снова на массив строк уже подготовленный текст
            $arr = explode("\n", $formatted_promise_text);

            // Расчетная высота смещения новой строки
            $height_tmp = $av_top_offset + $sign_height + $av_bottom_offset; # высота аватара + отступ (50px)

            //Выводить будем построчно с нужным смещением относительно левой границы
            foreach($arr as $str) {

                // Накладываем текст на картинку с учетом смещений
                #imagettftext($im, $font_size, 0, 50 + $left_x, 50 + $height_tmp, $black, $font_path, $str); // 50 - это отступы от края
                $img->text($str, $img_width/2, $height_tmp, function($font) use ($font_path, $font_size, $font_color) {
                    $font->file($font_path);
                    $font->size($font_size);
                    $font->color($font_color);
                    $font->align('center');
                    $font->valign('top');
                    //$font->angle(45);
                });

                // Смещение высоты для следующей строки
                $height_tmp = $height_tmp + $font_size * 1.5;
            }
        }


        $sign_result = $file_sign . '_sign.png';
        $img->save($sign_result);
        $img->destroy();

        #header('Content-Type: image/png');
        #echo $img->encode('png');

        /**
         * Отправляем на почту юзеру
         */
        Mail::send('emails.signature-app', compact('sign_result', 'email'), function ($message) use ($email, $sign_result) {

            $message->from(Config::get('mail.signature-app.address'), Config::get('mail.signature-app.name'));
            $message->subject(Config::get('mail.signature-app.subject'));
            $message->to($email);

            /**
             * Прикрепляем файл
             */
            /*
            if (Input::hasFile('file') && ($file = Input::file('file')) !== NULL) {
                #Helper::dd($file->getPathname() . ' / ' . $file->getClientOriginalName() . ' / ' . $file->getClientMimeType());
                $message->attach($file->getPathname(), array('as' => $file->getClientOriginalName(), 'mime' => $file->getClientMimeType()));
            }
            #*/
        });

        if ($remove_sign_file)
            unlink($file_sign);

        unlink($sign_result);

        return 1;
    }

}




function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    #echo "You have CORS!";
}