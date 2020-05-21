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
  	@Date:   2019-03-20 15:44:41
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-08-11 17:27:48

*/
function poster_auto_generation( $post_id, $post ){
  	
  	if( get_field('api_generation_poster', 'option') == 1 ){

	  	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	  	if ( wp_is_post_revision( $post_id ) ) return;
	    
	    $post_created  = new DateTime( $post->post_date_gmt );
	    $post_modified = new DateTime( $post->post_modified_gmt );

	    if( $post->post_status == 'publish' && $post->post_type == 'post' ){

	 		$cloud_poster_automation = get_field( 'cloud_poster_automation', 'options');

		    if( abs( $post_created->diff( $post_modified )->s ) <= 1 ){

		    	if( $cloud_poster_automation['auto_generation_poster'] == 1 ){
		        	asyn_post( admin_url('admin-ajax.php?action=create-bigger-image'), 'id=' . $post_id );
		    	}

		    }else{
		       
		       	if( $cloud_poster_automation['auto_update_poster'] == 1 ){
			    	delete_post_poster( $post_id );
			    	asyn_post( admin_url('admin-ajax.php?action=create-bigger-image'), 'id=' . $post_id );
			    }

		    }

		}
		
	}
}
add_action('save_post', 'poster_auto_generation', 10, 2 );


function del_cloud_share_cover_meta( $id ){

	$atta = get_post( $id ); 

	if( isset( $atta->post_parent ) && is_numeric( $atta->post_parent ) ){
		delete_post_meta( $atta->post_parent, 'bigger_cover');
	}

}
add_action( 'delete_attachment', 'del_cloud_share_cover_meta' );

function delete_post_poster( $post_id ){


	global $wpdb;
	$results = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '$post_id' AND post_title LIKE '%-cloud-poster'" );

	if( !empty( $results ) ){
		foreach ($results as $key => $value) {
			wp_delete_attachment( $value->ID );
		}
	}
	update_post_meta( $post_id, 'bigger_cover', '' );

	if( delete_post_meta( $post_id, 'bigger_cover' ) ){
		return true;
	}else{
		return false;
	}
	
}

function del_cloud_poster(){
  
	if( !current_user_can('manage_options') ) die('蛤？');

	$post_id = is_numeric( $_POST['id'] ) ? $_POST['id'] : 0;

	if( $post_id ){

		if( delete_post_poster( $post_id ) ){

			$msg = array(
				's' => 200
			);

		}else{

			$msg = array(
				's' => 404
			);

		}

		echo json_encode( $msg );

	}
	
	die();
}
add_action( 'wp_ajax_del-poster', 'del_cloud_poster');

function cloud_poster_count(){

	if( !current_user_can('manage_options') ) die('蛤？');

	if( $_POST['a'] == 'generate' ){

		$args = array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'meta_query'  => array(
				array(
					'key'     => 'bigger_cover',
					'compare' => 'NOT EXISTS'
				)
			)
		);

		$all_generate = new WP_Query( $args );

		if( $all_generate->found_posts > 0 ){
			echo json_encode( array(
				's'     => 200,
				'count' => $all_generate->found_posts
			) );
		}else{
			echo json_encode( array(
				's' => 404
			) );
		}


	}

	if( $_POST['a'] == 'del' ){

		$args = array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'meta_query'  => array(
				array(
					'key'     => 'bigger_cover',
					'compare' => 'EXISTS'
				)
			)
		);

		$all_generate = new WP_Query( $args );

		if( $all_generate->found_posts > 0 ){
			echo json_encode( array(
				's'     => 200,
				'count' => $all_generate->found_posts
			) );
		}else{
			echo json_encode( array(
				's' => 404
			) );
		}

	}

	die();
}
add_action( 'wp_ajax_poster-count', 'cloud_poster_count' );



function cloud_generate_poster_task(){

	if( !current_user_can('manage_options') ) die('蛤？');

	$args = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'showposts'    => 1,
		'meta_query'  => array(
			array(
				'key'     => 'bigger_cover',
				'compare' => 'NOT EXISTS'
			)
		)
	);

	$all_generate = new WP_Query( $args );

	if ( $all_generate->have_posts() ) : 
		while ( $all_generate->have_posts() ) : $all_generate->the_post();
			
			$msg = array(
				's'     => 200,
				'id'    => get_the_ID(),
				'nonce' => wp_create_nonce('mi-create-bigger-image-'.get_the_ID() )
			);

		endwhile;
	else :

		$msg = array(
			's' => 404
		);

	endif;
	echo json_encode( $msg );
	die();
}
add_action( 'wp_ajax_generate_poster_task', 'cloud_generate_poster_task' );



function cloud_del_poster_task(){

	if( !current_user_can('manage_options') ) die('蛤？');

	$args = array(
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'showposts'    => 1,
		'meta_query'  => array(
			array(
				'key'     => 'bigger_cover',
				'compare' => 'EXISTS'
			)
		)
	);

	$all_generate = new WP_Query( $args );

	if ( $all_generate->have_posts() ) : 
		while ( $all_generate->have_posts() ) : $all_generate->the_post();
			
			$msg = array(
				's'     => 200,
				'id'    => get_the_ID()
			);

		endwhile;
	else :

		$msg = array(
			's' => 404
		);

	endif;
	echo json_encode( $msg );
	die();

}
add_action( 'wp_ajax_del_poster_task', 'cloud_del_poster_task' );

