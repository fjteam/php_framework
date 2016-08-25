<?php

/**
 * Created by PhpStorm.
 * User: Tony
 * Date: 15/12/18
 * Time: 上午11:36
 */


abstract class m_page_abstract
{
	var $tpl_filename;
	var $request;

	function __construct($tpl_filename)
	{
		$this->tpl_filename = $tpl_filename;

		$this->request = new m_page_request();
	}

	function __destruct()
	{
		//在析构时自动输出
		$this->render();
	}

	function render()
	{
		$m_page_h5cache_obj = new m_page_h5cache_class();

		/**
		 * 解决缓存更新后自动刷新导致短时间内重复请求manifest文件导致的重复加载
		 */
		$manifest_loaded_content =$m_page_h5cache_obj->fetch_manifest_cached_loaded_content_and_clean();
		if (!empty($manifest_loaded_content)) {
			echo $manifest_loaded_content;
			return;
		}


		$in_tpl_data_arr=array();
		$in_js_data_arr=array();

		$tpl = new QuickSkin($this->tpl_filename);


		$this->on_set_data( $in_tpl_data_arr, $in_js_data_arr);

		$tpl->assign($in_tpl_data_arr);
		$tpl->assign("data",@json_encode($in_js_data_arr));



		if ($_SERVER["REQUEST_METHOD"]=="GET") {
			$tpl->assign("cache_manifest_url", $m_page_h5cache_obj->get_manifest_url());
		}

		$content = $tpl->result();
		$m_page_h5cache_obj->set_manifest_cached_content($content);

		echo $content;
	}



	/**
	 * 继承类后实现这个方法,当在模板输出前被调用
	 *
	 * @param $tpl_data_arr     //引用,模板变量数组
	 * @param $js_data_arr      //引用,js变量数组
	 * @return bool             //返回false则不输出
	 */
	abstract function on_set_data(&$set_tpl_data_arr, &$set_js_data_arr);

}


class m_page_request
{
	var $GET;
	var $POST;
	var $INPUT;

	function __construct()
	{
		global $_GET,$_POST,$_INPUT;

		if ($_SERVER["REQUEST_METHOD"]=="GET")
		{
			$this->GET =  !empty($_INPUT) ?  $_INPUT : $_GET;
		}
		else if ($_SERVER["REQUEST_METHOD"]=="POST")
		{
			$this->POST =  !empty($_INPUT) ?  $_INPUT : $_POST;
		}

		$this->INPUT = $_INPUT;
	}
}