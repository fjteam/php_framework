<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 15/12/18
 * Time: 下午11:55
 */
class m_page_h5cache_class
{
	private $session_check_key;
	private $request_uri;

	function __construct($request_uri="")
	{
		if (empty($request_uri))
		{
			$request_uri= $_SERVER["REQUEST_URI"];
		}
		$this->request_uri = $request_uri;
		$this->session_check_key = md5($request_uri);
	}


	function get_manifest_url()
	{
		$hash = md5($this->session_check_key.$this->request_uri."_m_hash_");
		$url = "/cache_manifest.php?url=".urlencode(base64_encode($this->request_uri))."&key=".$this->session_check_key."&hash={$hash}";

		return $url;
	}

	function check_manifest_url($url,$key,$hash)
	{
		return ($hash ==md5($key.$url."_m_hash_"));
	}



	function echo_manifest_content()
	{
		header('Content-Type: text/cache-manifest');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");

		$page_url = $this->request_uri;
		

		//附属的js\css\静态图片

		//区分目录处理 plan
		$menu = explode('/', trim($page_url, '/'));
		if ( $menu[0] == 'plan' ) {
			$content = $this->_get_page_realtime_content();
			$assert_list = $this->_get_page_assets();

			if ($assert_list)
			{
				$assert_url_list_str = join("\r\n",$assert_list);
			}
		} else {
			
			$url_info = $this->_get_url_details($page_url);

			ob_start();
			$_INPUT = $url_info['param'];
			require(G_M.$url_info['file']);
			$content =  ob_get_contents();
			ob_end_clean();

			$assert_list = array(
				'/index.php' => "/img/scroll_img/wap_index_new_user.jpg;/img/scroll_img/nian1000_index_2016.jpg?20160202;/mobile-fp-iconfont/fonts/mobile-fp.woff?201508191845;/mobile-fp-iconfont/fonts/mobile-fp.ttf?201508191845;/js/_catch_performance.js;/img/ft_customer.png;/img/index/ord_list_new.png;/img/index/logo_new_2016.png;/img/index/reserved_new.png;/img/index/phone_number_new.png;/img/index/reparation_new.png;/css/global.css?v9993;/css/index/globals_sf.css?v9992;/css/book.css?v9993;/css/new_css_rem/header_min.css?v9991;/css/index/footer.css;/css/new/share-btn.css?v=5;/js/zepto-all.min.js;/css/index/main_t.css?20151104;/css/time_picker.css?1220v4;/css/index/header_t.css?201508192037;/mobile-fp-iconfont/font_style.css?201508192037;/io_css.php;/css/index/order.css?v1;/css/swiper.min.css;/img/jiazhao/banner.jpg;/w/new_index/img/order/loading-50.gif;/js/zepto.flickable.js;/js/swiper.min.js;/js/m_zzche.js;/js/m_index_new_version.js?v412;/img/scroll_img/wtopimg_new.jpg?v1;/img/index/new_guide.png;/img/index/faq_guide.png;http://img.zuzuche.com/upload/201604/20160406175721300.jpg?imageView2/2/w/460/q/80;http://img.zuzuche.com/upload/201601/20160129194147284.jpg?imageView2/2/w/460/q/80;http://img.zuzuche.com/upload/201601/20160122184249276.jpg?imageView2/2/w/460/q/80;http://img.zuzuche.com/upload/201603/20160316122622874.png?imageView2/2/w/460/q/80;http://img.zuzuche.com/upload/201512/20151231181443862.jpg?imageView2/2/w/460/q/80;http://w.zuzuche.com/io_js.php?file=m_index;/js/tj_m.js?v20151109",
				'/w/select_city.php' => "/css/global.css?v9993;/css/index/globals_sf.css?v9992;/css/book.css?v9993;/css/new_css_rem/header_min.css?v9991;/css/index/footer.css;/css/new/share-btn.css?v=5;/js/zepto-all.min.js;/css/new_css_rem/header_old.css;/js/jquery-1.7.2.min.js;/js/m_zzche.js;/js/tj_m.js?v20151109;/js/_catch_performance.js;/img/ico_back.png;/img/ico_index.png;/img/search_icon.png;/img/del_btn.png;/js/fastclick.script.js",
			);
			// $str = '/css/global.css?v9993;/css/index/globals_sf.css?v9992;/css/book.css?v9993;/css/new_css_rem/header_min.css?v9991;/css/index/footer.css;/css/new/share-btn.css?v=5;/js/zepto-all.min.js;/css/index/main_t.css?20151104;/css/time_picker.css?1220v4;/css/index/header_t.css?201508192037;/mobile-fp-iconfont/font_style.css?201508192037;http://w.zuzuche.com/io_css.php;/css/index/order.css?v1;/css/swiper.min.css;/img/jiazhao/banner.jpg;/w/new_index/img/order/loading-50.gif;/js/zepto.flickable.js;/js/swiper.min.js;/js/m_zzche.js;/js/m_index_new_version.js?v412;/img/scroll_img/wtopimg_new.jpg?v1;/img/index/new_guide.png;/img/index/faq_guide.png;http://imgcdn1.zuzuche.com/upload/201604/20160406175721300.jpg?imageView2/2/w/460/q/80;http://imgcdn1.zuzuche.com/upload/201601/20160129194147284.jpg?imageView2/2/w/460/q/80;http://imgcdn1.zuzuche.com/upload/201601/20160122184249276.jpg?imageView2/2/w/460/q/80;http://imgcdn1.zuzuche.com/upload/201603/20160316122622874.png?imageView2/2/w/460/q/80;http://imgcdn1.zuzuche.com/upload/201512/20151231181443862.jpg?imageView2/2/w/460/q/80;http://w.zuzuche.com/io_js.php?file=m_index;/js/tj_m.js?v20151109;/js/_catch_performance.js;/img/ft_customer.png;/img/index/reparation_new.png;/img/index/phone_number_new.png;/img/index/reserved_new.png;/mobile-fp-iconfont/fonts/mobile-fp.ttf?201508191845;/img/index/logo_new_2016.png;/img/index/ord_list_new.png';
			$assert_url_list_str = join("\r\n",explode(";", $assert_list[$url_info['file']]));

			// $this->_get_assets_from_content($content);

			// $assert_url_list_str = file_get_contents("http://".M_DOMAIN."/include/manifest_assert/{$menu[0]}_conf.txt");
		}


		

		$page_output_hash = md5($content);


		echo "CACHE MANIFEST

#cache_hash: {$page_output_hash}

#====================================================
CACHE:
# 需要缓存的列表
#本页面
{$page_url}
#附属的js css 静态图片
{$assert_url_list_str}

#====================================================
NETWORK:
# 不需要缓存的
/cache_manifest.php
*

#====================================================
FALLBACK:
# 访问缓存失败后，备用访问的资源，第一个是访问源，第二个是替换文件*.html /offline.html



";

	}


	function _get_url_details($url) {
		$urlarr = explode('?', $url);
		$info 	= array('file'=>$urlarr[0], 'param'=>array());
		$parr   = explode('&',ltrim(ltrim($url, $urlarr[0]),'?'));

		$info['hash'] = _get_hashcode($info['file']);
		if ( $info['file']{strlen($info['file'])-1} == '/' ) $info['file'] .= 'index.php';

		foreach ($parr as $value) {
			$_parr = explode('=', $value);
			$info['param'][$_parr[0]] = $_parr[1];
		}

		$info['url'] = trim($url, '&');
		return $info;
	}



	function _get_assets_from_content($content) {

		$content = str_replace("'", '"', $content);
		

		preg_match_all("/href=\"(.*?)\"/is",$content,$match);

		// var_dump($match[1]);

		preg_match_all("/src=\"(.*?)\"/is",$content,$match);

		// var_dump($match[1]);

		return $ret;
	}	



	function set_manifest_cached_content($output)
	{
		global $MSO_COMMON_MEMCACHE_OBJ;

		$_cache_data = array();
		$_cache_data['update_time'] = time();
		$_cache_data['output']=$output;
		$_SESSION[md5($this->request_uri)]=$_cache_data;

		//同时写一份去memcache
		if (is_object($MSO_COMMON_MEMCACHE_OBJ))
		{
			$cache_key= "M_".$_COOKIE['user_action_id']."_".$this->session_check_key;
			$MSO_COMMON_MEMCACHE_OBJ->save_cache($cache_key, $_cache_data, 3600*24*30);
		}
	}


	/**
	 * 解决缓存更新后自动刷新导致短时间内重复请求manifest文件导致的重复加载
	 */
	function fetch_manifest_cached_loaded_content_and_clean($cache_time=3,$clean_after_get=true)
	{
		global $MSO_COMMON_MEMCACHE_OBJ;

		$ret = false;


		if (isset($_SESSION[md5($this->request_uri)])) {
			$tmp = $_SESSION[md5($this->request_uri)];
		}
		else if (is_object($MSO_COMMON_MEMCACHE_OBJ)) {
			$cache_key= "M_".$_COOKIE['user_action_id']."_".$this->session_check_key;
			$tmp = $MSO_COMMON_MEMCACHE_OBJ->get_cache($cache_key);
		}
		else
		{
			return false;
		}


		if (time() - $tmp['update_time'] <= $cache_time && !empty($tmp['output']))
		{
			$ret = $tmp['output'];
		}
		else
		{
			if ($clean_after_get)
			{
				unset($_SESSION[md5($this->request_uri)."_next_output"]);
			}
		}



		return $ret;
	}


	/**
	 * 读取页面编译后的静态资源列表assets-map.json, 获取页面依赖的js\css\静态图片等资源
	 * @return array
	 */
	private function _get_page_assets()
	{
		$ret = array();

		$dist_dir= dirname($_SERVER['DOCUMENT_ROOT'].$this->request_uri)."/dist/";

		$assets_map_file = $dist_dir."assets-map.json";

		if (is_file($assets_map_file))
		{
			$assets_list = json_decode(file_get_contents($assets_map_file));

			if (!empty($assets_list->assets))
			{
				foreach ($assets_list->assets as $v)
				{
					if (preg_match("/(.css|.js|.png|.jpg)$/",$v))
					{
						$ret[]= $v;
					}
				}
			}
		}

		return $ret;
	}


	private function _get_page_realtime_content()
	{

		$old_server_uri = $_SERVER["REQUEST_URI"];
		$_SERVER["REQUEST_URI"] = $this->request_uri;

		$zzc_url_router_obj = new zzc_url_router_class();

		ob_start();
		$zzc_url_router_obj->run();
		$output =  ob_get_contents();
		ob_end_clean();


		$_SERVER["REQUEST_URI"] = $old_server_uri;

		return $output;
	}



}