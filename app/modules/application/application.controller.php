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

        $app_path = public_path('uploads/app-signature');

        $file_bg = $app_path . '/bg.png';
        $file_font = $app_path . '/webfont.ttf';
        $file_sign = $app_path . '/sign.png';

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

        $text = strip_tags(str_replace('</p><p>', "\n\n", $text));

        /***************************************************************************************/

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


        #$img->save($dest_path);
        #$img->destroy();

        header('Content-Type: image/png');
        echo $img->encode('png');
    }

}