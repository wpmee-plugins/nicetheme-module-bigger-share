<?php

/*
            /$$
    /$$    /$$$$
   | $$   |_  $$    /$$$$$$$
 /$$$$$$$$  | $$   /$$_____/
|__  $$__/  | $$  |  $$$$$$
   | $$     | $$   \____  $$
   |__/    /$$$$$$ /$$$$$$$/
          |______/|_______/
================================
        Keep calm and get rich.
                    Is the best.

  	@Author: Dami
  	@Date:   2018-10-08 14:21:45
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-11 13:11:08
  	Plugin Name: 云海报客户端
	Plugin URI: https://www.nicetheme.cn/modules/bigger-share-cloud
	Description: nicetheme提供的云端海报生成服务
	Version: 1.0.3
	Author URI: http://www.nicetheme.cn
	Nicetheme Module: bigger-share-module
	Compatible: 
*/

define( 'NC_BIGGER_SHARE_MODULE_DIR', dirname(__FILE__) );
define( 'NC_BIGGER_SHARE_MODULE_URL', plugins_url( '', __FILE__ ) );
define( 'NC_CLOUD_POSTER_API', 'https://cloud-poster-api.nicetheme.xyz');
// define( 'NC_CLOUD_POSTER_API', 'http://debug.wp');
define( 'NC_CLOUD_POSTER_VERSION', '1.0.3' );

add_action( 'plugins_loaded', 'nicetheme_bigger_share_module_init' );

function nicetheme_bigger_share_module_init(){

	// nc store check
	if( !defined('NC_STORE_ROOT_PATH') ){

		add_action( 'admin_notices', 'nicetheme_bigger_share_module_init_check' );
		function nicetheme_bigger_share_module_init_check(){
			$html = '<div class="notice notice-error">
				<p><b>错误：</b> 云海报 缺少依赖插件 <code>nicetheme 积木</code> 请先安装并启用 <code>nicetheme 积木</code> 插件。</p>
			</div>';
			echo $html;
		}
		

	}else{

		acf_add_options_sub_page(
			array(
				'page_title'      => '云海报 积木',
				'menu_title'      => '云海报 积木',
				'menu_slug'       => 'nc-bigger-share-module-options',
				'parent_slug'     => 'nc-modules-store',
				'capability'      => 'manage_options',
				'update_button'   => '保存',
				'updated_message' => '设置已保存！'
			)
		);


		add_filter('nc_save_json_paths', 'nicetheme_bigger_share_module_json_save_point');

		function nicetheme_bigger_share_module_json_save_point( $path ) {

		    // update path
		    $path[] = NC_BIGGER_SHARE_MODULE_DIR . '/conf';

		    // return
		    return $path;

		}

		add_filter('acf/settings/load_json', 'nicetheme_bigger_share_module_json_load_point');

		function nicetheme_bigger_share_module_json_load_point( $paths ) {

		    // append path
		    $paths[] = NC_BIGGER_SHARE_MODULE_DIR . '/conf';

		    // return
		    return $paths;

		}

		include('acf.php');

		include('functions.php');

		include('ajax/load.php');

		include('automation.php');

	}

}