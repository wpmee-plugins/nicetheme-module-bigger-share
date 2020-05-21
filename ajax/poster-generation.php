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
  	@Date:   2019-02-22 23:01:13
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-11 13:47:19

*/

function cloud_poster_create(){
	global $post;
	$post_id = sanitize_text_field( $_POST['id'] );
	$post = get_post($post_id);
	setup_postdata( $post );

	$poster = get_post_meta( $post_id, 'bigger_cover', true );

	if( empty( $poster ) ){
		$poster_config = get_cloud_poster_config( $post_id, true );
	}else{	
		$poster_config = get_cloud_poster_config( $post_id, false );
	}

	set_query_var( 'cloud_poster_data', $poster_config );

	$Share = new MiShare();

	if( !empty( $poster ) ){

		$Share->config = array( 'url' => $poster_config['url'], 'title' => $poster_config['title'], 'pic' => $poster, 'des' => $poster_config['description'] );
		
		$html = '
		<div class="cover-image">
        	<img src="' . $poster . '">
		</div>
    	<div class="cover-share">
        	<a class="weibo" href="'.$Share->weibo().'" data-toggle="tooltip"target="_blank" data-original-title="分享至微博"><i class="iconfont icon-weibo"></i></a>
        	<a class="qq" href="'.$Share->qq().'" data-toggle="tooltip" target="_blank" data-original-title="分享至 QQ"><i class="iconfont icon-qq"></i></a>        
        	<a href="' . $poster . '" download="'.$poster_config['title'].'-Bigger-cover.png" data-toggle="tooltip" data-original-title="下载海报"><i class="iconfont icon-download"></i></a>
    	</div>
    	<div class="cover-text">分享朋友圈请先下载海报</div>';

		echo json_encode( array(
			'status'     => 200,
			'head_image' => preg_replace( '/http[s]?:/', '', get_query_var( 'cloud_poster_data' )['head_image'] ),
			'html'       => $html,		
		) );
		die();

	}else{
   
		$prefix = time() . '-' . $post_id;

		$poster_name = $prefix .'-cloud-poster.png';

		set_query_var( 'poster_name', $poster_name );

		add_filter( 'http_request_args', 'get_cloud_poster_headers', 10, 2 );

		if( !upload_cloud_poster_config( $poster_name ) ){
			echo json_encode( array(
			    'status' => 500,
			    'msg'    => '信息上传失败~'
			) );
			die();
		}

		add_filter( 'http_response', 'cloud_poster_http_response', 10, 3 );	

		$poster = media_sideload_image( NC_CLOUD_POSTER_API . '/wp-json/nicetheme-bigger-share-service/v1/create?' . $poster_name, $post_id, null, 'src' );
		$result = get_query_var( 'cloud_poster_status' );

		if( $result['status'] == 200 ){

          	if( !is_wp_error( $poster ) ){

          		$poster_config = get_cloud_poster_config( $post_id, false );
				set_query_var( 'cloud_poster_data', $poster_config );
    
				add_post_meta( $post_id, 'bigger_cover', $poster );

				$Share->config = array( 'url' => $poster_config['url'], 'title' => $poster_config['title'], 'pic' => $poster, 'des' => $poster_config['description'] );

				$html = '
				<div class="cover-image">
				  <img src="' . $poster . '">
				</div>
				<div class="cover-share">
				  <a class="weibo" href="'.$Share->weibo().'" data-toggle="tooltip"target="_blank" data-original-title="分享至微博"><i class="iconfont icon-weibo"></i></a>
				  <a class="qq" href="'.$Share->qq().'" data-toggle="tooltip" target="_blank" data-original-title="分享至 QQ"><i class="iconfont icon-qq"></i></a>        
				  <a href="' . $poster . '" download="'.$poster_config['title'].'-Bigger-cover.png" data-toggle="tooltip" data-original-title="下载海报"><i class="iconfont icon-download"></i></a>
				</div>
				<div class="cover-text">分享朋友圈请先下载海报</div>';

				echo json_encode( array(
				  'status'     => 200,
				  'head_image' => preg_replace( '/http[s]?:/', '', get_query_var( 'cloud_poster_data' )['head_image'] ),
				  'html'       => $html
				) );

				}else{

					echo json_encode( array(
					    'status' => 500,
					    'msg'    => '海报拉取失败！'
					) );

				}

				die();

		}else{


			echo json_encode( array(
				'status' => $result['status'],
				'msg'    => $result['msg']
			) );

			die();
		}

	}
	die();
}
add_action('wp_ajax_nopriv_create-bigger-image', 'cloud_poster_create');
add_action('wp_ajax_create-bigger-image', 'cloud_poster_create');

