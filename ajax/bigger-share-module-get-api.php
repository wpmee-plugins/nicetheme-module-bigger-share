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
  	@Date:   2019-02-22 15:48:42
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-09 21:36:19

*/

function bigger_share_module_get_api(){

	if( !current_user_can( 'manage_options' ) ){
		echo '<p>无权查看<p>';
		die();
	}

	$nc_store_user_data = get_option('NC_STORE_USER_DATA');

	if( empty( $nc_store_user_data ) ){

		echo json_encode( array(
			'status' => 500,
			'html'   => '<p>请先登录 <b>nicetheme 积木</b> 并保持登录状态，否则无法远程生成海报！</p>'
		) );

		die();
	}


	$statusRequest = new NicethemeStoreRequest(
	'get-my-bigger-share-status',
		array(
			'method' => 'POST',
			'body' => array(
			)
		)
	);

	$status = json_decode( $statusRequest->request(), true ); 
	
	if( !$status ){
		echo json_encode( array(
			'status' => 500,
			'html'   => '<p>远程 API 通信失败，请刷新页面</p>'
		) );
		die();
	}

	if( $status['status'] != 200 ){

		echo json_encode( array(
			'status' => 500,
			'html'   => '<p>'.$status['msg'].'</p>'
		) );

		die();
	}

	if( $status['bigger_share_number'] > 0 ){

		echo json_encode( array(
			'status' => 200,
			'html'   => '<p>状态正常，您目前还能生成 ' . $status['bigger_share_number'] . ' 次海报</p>'
		) );

	}else{
		echo json_encode( array(
			'status' => 200,
			'html'   => '海报生成次数不足，暂时无法远程生成海报。请及时充值。'
		) );
	}

	die();
}
add_action( 'wp_ajax_bigger_share_module_get_api', 'bigger_share_module_get_api' );