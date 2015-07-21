<?php

class AdminInstagramController extends BaseController {

    public static $name = 'instagram';
    public static $group = 'instagram';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        Route::any('get/instagram/approved', $class . '@getInstagramApproved');
        Route::group(array('before' => 'auth', 'prefix' => $prefix), function() use ($class) {
            Route::controller($class::$group, $class);
        });
    }

    ## Shortcodes of module
    public static function returnShortCodes() {
        ##
    }
    
    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        return array(
        	'view'      => 'Просмотр',
        	'add'       => 'Добавление',
        	'approve'   => 'Одобрение',
        	'unapprove' => 'Снятие одобрения',
        	'delete'    => 'Удаление',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
        	'title' => 'Instagram',
            'visible' => 1,
        );
    }

    ## Menu elements of the module
    public static function returnMenu() {
        return array(
            array(
            	'title' => 'Instagram',
                'link' => self::$group,
                'class' => 'fa-instagram',
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
        );

        View::share('module', $this->module);
	}

	public function getIndex(){
		
        $view = Input::get('view');
		return View::make($this->module['tpl'].'index', compact('photos', 'view'));
	}

    /************************************************************************************/

	public function getAdd(){

        #$hashtags = Settings::get('instagram_hashtags');
        #if (!$hashtags)
            $hashtags = Config::get('site.instagram_hashtags');

		return View::make($this->module['tpl'].'add', compact('hashtags'));
	}

	public function postAdd(){

        #Helper::dd(link::auth($this->module['rest']."/list?view=unapproved"));

        $hashtags = array();
        $temp = Input::get('hashtags');
        $temp = explode("\n", $temp);
        foreach($temp as $t => $tmp) {
            $tmp = trim($tmp);
            if ($tmp)
                $hashtags[] = $tmp;
        }
        #Helper::dd($hashtags);
        Settings::set('instagram_hashtags', implode("\n", $hashtags));

        foreach ($hashtags as $h => $hashtag) {

            #Helper::dd($hashtags);

            $feed = $this->getInstaFeed($hashtag);


            #$feed = $feed['data'];

            if ( !isset($feed['data']) || !is_array($feed['data']) || !count($feed['data']) )
                continue;

            foreach ($feed['data'] as $post) {

                $input = array(
                    'instagram_id' => $post['id'],
                    'type' => $post['type'],
                    'user_id' => $post['user']['id'],
                    'image' => $post['images']['standard_resolution']['url'],
                    'link' => $post['link'],
                    'instagram_created_time' => $post['created_time'],
                    'full' => json_encode($post),
                );
    
                $insta = Instagram::where('instagram_id', $post['id'])->first();
                if (is_object($insta))
                    $insta->update($input);
                else
                    $insta = Instagram::create($input);
    
                if (is_array($post['tags']) && count($post['tags'])) {
                    #$tags = array();
                    $vals = array();
                    foreach ($post['tags'] as $tag) {
                        $tag_ = array(
                            'instagram_id' => $post['id'],
                            'tag' => $tag
                        );
                        #$tags[] = $tag_;
                        $vals[] = "('{$post['id']}', '{$tag}')";
                    }

                    ## BCEM KOCTblJI9lM KOCTblJIb
                    $insta_tags = new InstagramTags;
                    DB::statement('INSERT IGNORE INTO ' . $insta_tags->table . ' VALUES ' . implode(", ", $vals) . '');

                    #InstagramTags::insert($tags);
                    #InstagramTags::firstOrCreate($tags);
                }

                #Helper::dd($feed['data'][0]);

            }

        }

        Redirect(link::auth($this->module['rest']."/list?view=unapproved"));

	}

    private function getInstaFeed($hashtag) {

    	//Query need client_id or access_token
    	$query = array(
    		'client_id' => '86bf1e5c98f64d4cafb5dbcaafb3820c',
    		'count'		=> 10
    	);
    
    	$url = 'https://api.instagram.com/v1/tags/'.urlencode($hashtag).'/media/recent?'.http_build_query($query);

	  	try {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$json = curl_exec($ch);
			curl_close($ch);    		 
		} catch(Exception $e) {
			return $e->getMessage();
		}
    	  
    	return json_decode($json, true);
    }


    /************************************************************************************/
    
	public function getList(){

		$status = 0;
        $view = Input::get('view');
        switch ($view) {
            case 'approved':
                $status = 1;
                break;
            case 'banned':
                $status = 2;
                break;
            case 'unapproved':
            default:
                $status = 0;
                break;
        }

        $limit = Config::get('site.paginate_limit', 10);
        $posts = Instagram::where('status', $status)->orderBy('updated_at', 'DESC')->paginate($limit);

		return View::make($this->module['tpl'].'list', compact('view', 'posts', 'limit'));
	}


    /************************************************************************************/

    /*
	public function deleteDestroy($id){

		if(!Request::ajax())
            return App::abort(404);

		$json_request = array('status'=>FALSE, 'responseText'=>'');
        $user_photo = UserPhoto::find($id);
	    $user_photo->delete();

        #$photo_id = $user_photo->photo_id;
        #$photo = Photo::find($photo_id);
        #$photo->delete();
		#@unlink(public_path().Config::get('app-default.galleries_photo_dir')."/".$photo->name);
		#@unlink(public_path().Config::get('app-default.galleries_thumb_dir')."/".$photo->name);

        @unlink(Config::get('site.tmp_dir')."/".$user_photo->image);

		$json_request['responseText'] = 'Фото удалено';
		$json_request['status'] = TRUE;
		return Response::json($json_request,200);
	}
    */

	public function postApprove($id){
		$json_request = array('status'=>TRUE, 'responseText'=>'');

        #$id = Input::get('id');

		if($obj = Instagram::where('id', $id)->first()) {
			$obj->update(array('status' => Input::get('value')));
			$obj->touch();
		}

		if(Input::get('value') == 1):
			$json_request['responseText'] = "Одобрено";
		else:
			$json_request['responseText'] = "Снято с публикации";
		endif;
		return Response::json($json_request,200);
    }

	public function postBanned($id){
		$json_request = array('status'=>TRUE, 'responseText'=>'Отклонено');

        #$id = Input::get('id');

		if($obj = Instagram::where('id', $id)->first()) {
			$obj->update(array('status' => 2));
			$obj->touch();
		}

		return Response::json($json_request,200);
    }


    public function getInstagramApproved() {

        $tags = Input::get('tags');
        $limit = (int)abs(Input::get('limit')) ?: 0;

        if (!is_array($tags))
            $tags = (array)$tags;

        #Helper::tad($tags);

        $temp = InstagramTags::whereIn('tag', $tags)->lists('tag', 'instagram_id');
        #Helper::tad($temp);

        $results = Instagram::where('status', 1)->whereIn('instagram_id', array_keys($temp))->orderBy('created_at', 'DESC')->get();
        #Helper::tad($results);

        if (isset($results) && count($results)) {

            foreach ($results as $r => $result) {

                $result['tag'] = $temp[$result->instagram_id];
                $results[$r] = $result;
            }

            #Helper::tad($results);
            $results = DicLib::groupByField($results, 'tag');
        }
        #Helper::tad($results);

        $return = [];

        $counts = [];
        if (isset($results) && count($results)) {

            foreach ($results as $tag => $photos) {

                if (isset($photos) && count($photos)) {

                    if (!isset($return[$tag])) {
                        $return[$tag] = [];
                        $counts[$tag] = 0;
                    }

                    foreach ($photos as $photo) {

                        $valid_image = false;

                        $curl = curl_init($photo->image);
                        curl_setopt($curl, CURLOPT_NOBODY, true);
                        $result = curl_exec($curl);
                        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);

                        #echo $statusCode . ' / ';

                        if ($limit == 0 || $counts[$tag] < $limit) {

                            $return[$tag][$photo->id] = [
                                'image' => $photo->image,
                                'link' => $photo->link,
                            ];

                            ++$counts[$tag];
                        }
                    }
                }

            }
        }

        #Helper::tad($return);

        return Response::json($return, 200);
    }
}


