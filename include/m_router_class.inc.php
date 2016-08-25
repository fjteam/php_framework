<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 15/12/18
 * Time: 上午11:38
 */

/**
 * m.zuzuche.com 入口总路由
 *
 *
 *
 * m.zuzuche.com/aaaaa/bbbb/cccc/?a=1&b=2
 *  服务器rewrite==>
 * m.zuzuche.com/index.php?q=/aaaaa/bbbb/cccc/?a=1&b=2
 *       include==>
 * ./aaaaaa/bbbb/ccccc/index.php?a=1&b=2  或  ./aaaaaa/bbbb/ccccc.php?a=1&b=2
 *
 *
 *
 *
 *
 * nginx rewrite 规则:

location ~ \/$ {
	index index.html index.htm index.php;

	set $flag "0";

	if ( !-e $request_filename) {
		set $flag "${flag}1";
	}

	if ($request_filename !~* \.){
		set $flag "${flag}1";
	}

	if ($flag = "011"){
		rewrite ^(.*)$ /index.php?q=$1 last;
	}

	if (!-e $request_filename) {
		rewrite /(.*?)\.html(.*)$  /$1.php$2 last;
	}

}

 *
 */


class zzc_url_router_class
{
	private $pages_base_dir;
	private $m_request_url;
	private $m_php_file;

	function __construct()
	{
		$this->init();
	}



	/**
	 * 转换 $_REQUEST \ $_GET \  $_SERVER等参数
	 */
	private function init()
	{
		/*
		 * 非rewrite模式,即需要写index.php传入:
		 * http://m.zuzuche.com/index.php?q=/aaaa/bbb/ccc/bbbbb?ddd=343&aaa=22
		 */
		if (preg_match("/^\/(?:index.php)?\?q=(([^?]*)(\?.*)?)$/i",$_SERVER['REQUEST_URI'],$m))
		{

			$_SERVER['REQUEST_URI']=$m[1];
			$_SERVER['QUERY_STRING']=$m[3];
			$this->m_request_url = $m[1];
			$this->m_php_file = $m[2];
		}
		else if (preg_match("/^(.*?)\.html(.*)$/i",$_SERVER['REQUEST_URI'],$m))         //用.html的扩展名访问 .php,需要服务器rewrite
		{
			$this->m_request_url = $m[1].".php".$m[2];
			$this->m_php_file = $m[1].".php";

		}
		else if (preg_match("/^(.*?)(\?.*)?$/i",$_SERVER['REQUEST_URI'],$m))
		{

			$_SERVER['REQUEST_URI']=$m[0];
			$_SERVER['QUERY_STRING'] = $m[2];

			$this->m_request_url = $m[2];
			$this->m_php_file = $m[1];

		}

		//处理$_GET等
		$url_info = parse_url($this->m_request_url);
		if (!empty($url_info["query"])) {
			parse_str($url_info["query"], $_GET);
			$_REQUEST = array_merge($_REQUEST, $_GET);

			global $_INPUT;
			$_INPUT = $_REQUEST;
		}



		/**
		 * 防止访问上级目录
		 */
		$this->m_php_file = str_replace("..","",$this->m_php_file);
		if ($this->m_php_file  =='/index.php' || $this->m_php_file  == '/')
		{
			$this->m_php_file ='index/index.php';
		}


		$this->pages_base_dir = $_SERVER["DOCUMENT_ROOT"]."/";


	}


	/**
	 * 路由执行
	 */
	function run($need_page_class)
	{
		$include_file = $this->pages_base_dir.$this->m_php_file;

		if (is_file($include_file))  //index/.php
		{

		}
		else if (is_dir($include_file)) //aaa/bbb/ ==> aaa/bbb/index.php
		{
			$include_file = $include_file."/index.php";
		}
		else if (is_file( substr($include_file,0,strlen($include_file)-1).".php"))   //aaa/bbb/ccc/ ==> aaa/bbb/ccc.php
		{
			$include_file = substr($include_file,0,strlen($include_file)-1).".php";
		}



		if (file_exists( $include_file ) ) {
			//配置包含路径环境
			ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.dirname($include_file));

			include_once G_M_PATH."./include/m_page_class.inc.php";

			//包含文件件
			require($include_file);
		} else {
			header("HTTP/1.1 404 Not Found");
			echo "404 找不到该页 /". str_replace($this->pages_base_dir,"",$include_file) ."";
			exit;
		}
	}
}
