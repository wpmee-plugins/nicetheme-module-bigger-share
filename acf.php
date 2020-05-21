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
  	@Date:   2019-02-22 14:19:19
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-01 13:43:06

*/
add_action('wp_loaded', 'nicetheme_bigger_share_module_info_init');

function nicetheme_bigger_share_module_info_init(){

	acf_add_local_field_group( array(
		'key'    => 'nicetheme_bigger_share_module_info',
		'title'  => '远程信息',
		'fields' => array(
			array(
				'key'     => 'bigger_share_module_api_info',
				'name'    => 'bigger_share_module_api_info',
				'type'    => 'message',
				'message' => '<div class="bigger_share_module_api_info"><span class="spinner" style="visibility: initial; float: none;"></span></div><style>#acf-group_5c6fc11feb1d4 { display: none; }</style>',
			)
		),
		'location' => array (
			array (
				array (
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'nc-bigger-share-module-options',
				),
			),
		),
		'hide_on_screen' => array(
			'the_content'
		)
	));	

}

function bigger_share_get_set_image( $img ){

	return NC_BIGGER_SHARE_MODULE_URL .'/conf/' . $img . '.png';

}

add_action( 'admin_footer', 'bigger_share_module_get_api_hook' );
function bigger_share_module_get_api_hook(){

	if( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'nc-bigger-share-module-options' ){
		echo '<script>
				jQuery.ajax({
					url: ajaxurl,
					type: "POST",
					dataType: "json",
					data: {action: "bigger_share_module_get_api"},
				})
				.done(function( data ) {
					if( data.status == 200 ){
						jQuery(".bigger_share_module_api_info").html(data.html);
						jQuery("#acf-group_5c6fc11feb1d4").show();
					}else{
						jQuery(".bigger_share_module_api_info").html(data.html);
					}
				})
				.fail(function() {
					layer.msg("网络错误，请刷新页面", {icon: 2});
				});
				
			</script>';
	}

}

