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
  	@Date:   2019-02-22 22:59:36
 * @Last Modified by: suxing
 * @Last Modified time: 2019-08-11 16:01:39

*/

function get_cloud_poster_config( $post_id, $download ){

	$style = get_field( 'generation_poster_style', 'options' );

	$post = get_post( $post_id );

	$data = array(
		'style' => $style
	);

	switch ( $style ) {
		case 'style-1':
			
			// $data['avatar'] = get_avatar_url( $post->post_author, array( 'size' => 200 ) );

			// if( $download ){

			// 	$data['avatar'] = nc_poster_get_image( $data['avatar'] );

			// 	if( !$data['avatar'] ){

			// 		echo json_encode( array(
	  //                   'status' => 500,
	  //                   'msg'    => '头像拉取失败！'
	  //               ) );
			// 		die();
			// 	}
			// }

			// $data['author'] = get_the_author_meta( 'display_name', $post->post_author );
			$cats = get_the_category( $post_id );
			$data['cat_name'] = $cats[0]->name;
			$data['slogan'] = get_field( 'cloud_poster_slogan', 'options' );

			break;

		case 'style-2':
			
			// $data['avatar'] = get_avatar_url( $post->post_author, array( 'size' => 200 ) );

			// if( $download ){

			// 	$data['avatar'] = nc_poster_get_image( $data['avatar'] );

			// 	if( !$data['avatar'] ){

			// 		echo json_encode( array(
	  //                   'status' => 500,
	  //                   'msg'    => '头像拉取失败！'
	  //               ) );
			// 		die();
			// 	}
			// }

			// $data['author'] = get_the_author_meta( 'display_name', $post->post_author );
			$cats = get_the_category( $post_id );
			$data['cat_name'] = $cats[0]->name;
			$data['slogan'] = get_field( 'cloud_poster_slogan', 'options' );

			break;
		
		default:
			# code...
			break;
	}

	

	$data['site_logo'] = get_field( 'cloud_poster_logo', 'options' );

	if( $download ){

		$data['site_logo'] = nc_poster_get_image( $data['site_logo'] );

		if( !$data['site_logo'] ){

			echo json_encode( array(
                'status' => 500,
                'msg'    => 'LOGO拉取失败！'
            ) );
			die();
		}
	}

	$data['title'] = get_field( 'cloud_poster_title', $post_id );

	if( empty( $data['title'] ) ){

		$data['title'] = $post->post_title;

	}

	$data['description'] = get_field( 'cloud_poster_description', $post_id );

	if( empty( $data['description'] ) ){

		if( empty( $post->post_excerpt ) ){

			$data['description'] = wp_trim_words( strip_tags( strip_shortcodes( $post->post_content ) ), 50 );

		}else{
			$data['description'] = strip_tags( $post->post_excerpt );
		}

	}

	$data['head_image'] = get_field( 'cloud_poster_head_image', $post_id );

	if( empty( $data['head_image'] ) ){

		if( has_post_thumbnail( $post_id ) ){

			$post_thumbnail_src = get_post_thumbnail_id($post_id);
	        $post_thumbnail_src =  wp_get_attachment_image_src($post_thumbnail_src, 'full');
	        $data['head_image'] = $post_thumbnail_src[0];

		}else{

			$preg =  '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
			preg_match_all($preg, apply_filters( 'the_content', $post->post_content ), $match);

			if( isset( $match[1][0] ) && !empty( $match[1][0] ) ){

				$data['head_image'] = $match[1][0];

			}else{

				$data['head_image'] = get_field( 'cloud_poster_default_head_image', 'options' );

			}

		}

	}

	if( $download ){

		$data['head_image'] = nc_poster_get_image( $data['head_image'] );

		if( !$data['head_image'] ){

			echo json_encode( array(
                'status' => 500,
                'msg'    => '头图拉取失败！'
            ) );
			die();
		}
	}

	$data['date'] = $post->post_date;

	$data['url'] = @get_permalink( $post_id );

	return $data;

}

function get_cloud_poster_headers( $args, $url ){

	$secret = get_option( 'NC_STORE_SECRET' ); 

	$args['headers'] = array( 
		'SOURCE'      => 'NICETHEME-STORE',
		'CALLBACKURL' => admin_url(),
		'SECRET'      => $secret
	);

	$args['body']['poster_name'] =  get_query_var( 'poster_name' );

	return $args;

}

function cloud_poster_http_response( $response, $r, $url ){

	$response_code = wp_remote_retrieve_response_code( $response );
	switch ( $response_code ) {

		case '200':
			set_query_var( 'cloud_poster_status', array(
				'status' => 200,
				'msg'    => '请求正常'
			) );
		break;

		case '201':
			set_query_var( 'cloud_poster_status', array(
				'status' => 201,
				'msg'    => '非法请求'
			) );
		break;

		case '202':
			set_query_var( 'cloud_poster_status', array(
				'status' => 202,
				'msg'    => '非法请求'
			) );
		break;

		case '203':
			set_query_var( 'cloud_poster_status', array(
				'status' => 203,
				'msg'    => '余额不足'
			) );
		break;

		default:
			set_query_var( 'cloud_poster_status', array(
				'status' => 998,
				'msg'    => '未知错误'
			) );
		break;



	}


	return $response;
}

function nc_poster_enqueue_script_frontend() {
	if (!is_admin() && ( is_single() || is_singular() ) ) {
		wp_register_script('nice-poster', NC_BIGGER_SHARE_MODULE_URL . '/static/nice-poster.js', array('jimu-js'), NC_CLOUD_POSTER_VERSION, true);
		wp_enqueue_script('nice-poster');
	}

}
add_action('wp_enqueue_scripts', 'nc_poster_enqueue_script_frontend');

function nc_poster_add_admin_scripts( $hook ) {
    
    wp_enqueue_script(  'poster-admin-main', NC_BIGGER_SHARE_MODULE_URL .'/static/admin-main.js' );

}
add_action( 'admin_enqueue_scripts', 'nc_poster_add_admin_scripts', 10, 1 );

function nc_poster_add_action_row( $actions, $post ){

    if ( $post->post_type == 'post' ){
    	$actions['generate_poster'] = '<a class="generatePoster" data-id="'.$post->ID.'" href="javascript:;">生成海报</a>';
    	$actions['delete'] = '<a class="delPoster" data-id="'.$post->ID.'" href="javascript:;">删除海报</a>';
        
    }
    return $actions;
}
add_filter('post_row_actions','nc_poster_add_action_row', 10, 2);

function nc_poster_get_image( $url ){
	
	$parse_url = parse_url($url);
	$file = substr( ABSPATH, 0 , -1 ) . $parse_url['path'];

	if( file_exists( $file ) ){

		$image = file_get_contents( $file );

		$file_type = nice_poster_file_type( $image );

		if( $file_type == 'jpg' || $file_type == 'png' || $file_type == 'gif' ){

			$base64 = 'data:image/' . $file_type . ';base64,' .  base64_encode( $image ); 

			return $base64;

		}else{
			return false;
		}


	}else{

		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Referer'    => home_url('/nicetheme-cloud-poster-service.newbee'),
				'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
			)
		) );

		$response_code = wp_remote_retrieve_response_code( $response );

		if( $response_code == 200 ){

			$body = wp_remote_retrieve_body( $response );

			$file_type = nice_poster_file_type( $body );

			if( $file_type == 'jpg' || $file_type == 'png' || $file_type == 'gif' ){

				$base64 = 'data:image/' . $file_type . ';base64,' .  base64_encode( $body ); 

				return $base64;

			}else{
				return false;
			}


		}else{

			return false;
		}

	}

}

function nice_poster_file_type( $str ){

   	$bin = substr( $str, 0, 2 );
    $strInfo = @unpack("C2chars", $bin);
    $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
    $fileType = '';
    switch ($typeCode){
        case 255216:
            $fileType = 'jpg';
            break;
        case 7173:
            $fileType = 'gif';
            break;
        case 13780:
            $fileType = 'png';
            break;
        default:
            $fileType = 'unknown: '.$typeCode;
    }

    if ($strInfo['chars1']=='-1' AND $strInfo['chars2']=='-40' ) return 'jpg';
    if ($strInfo['chars1']=='-119' AND $strInfo['chars2']=='80' ) return 'png';

    return $fileType;
}

function upload_cloud_poster_config( $poster_name ){

	$url = NC_CLOUD_POSTER_API . '/wp-json/nicetheme-bigger-share-service/v1/upload-poster-config';

	$response = wp_remote_post( $url, array(
		'timeout' => 30,
		'body' => array(
			'poster_name' => $poster_name,
			'poster_config' => get_query_var( 'cloud_poster_data' )
		)
	) );

	$response_code = wp_remote_retrieve_response_code( $response );

	if( $response_code == 200 ){
		return true;
	}else{
		return false;
	}

}


