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
  	@Date:   2018-08-26 20:24:05
  	@Last Modified by:   Dami
  	@Last Modified time: 2019-03-21 16:51:49
*/

jQuery(document).ready(function($) {
	
	$(document).on('click', '.generatePoster', function(event) {
		event.preventDefault();
		
		var that = $(this);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {action: 'create-bigger-image', id: that.data('id') },
		})
		.done(function( data ) {

			if( data.status == 200 ){
				alert('生成成功');
			}else{
				alert('生成失败');
			}
			
		})
		.fail(function() {
			alert("网络错误！");
		});
		

	});


	$(document).on('click', '.delPoster', function(event) {
		event.preventDefault();
		
		var that = $(this);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {action: 'del-poster', id: that.data('id') },
		})
		.done(function( data ) {

			if( data.s == 200 ){
				alert('删除成功');
			}else{
				alert('删除失败');
			}
			
		})
		.fail(function() {
			alert("网络错误！");
		});

	});

	$(document).on('click', '.generate-all-poster', function(event) {
		event.preventDefault();
		
		var that = $(this);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {action: 'poster-count', a: 'generate'},
		})
		.done(function( data ) {

			if( data.s == 200 ){

				that.data('count', data.count);

				generate_poster( that );

			}else{
				alert('没有需要生成的海报');
			}
			
		})
		.fail(function() {
			console.log("error");
		});
		

	});

	$(document).on('click', '.delete-all-poster', function(event) {
		event.preventDefault();
		
		var that = $(this);

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {action: 'poster-count', a: 'del'},
		})
		.done(function( data ) {

			if( data.s == 200 ){

				that.data('count', data.count);

				delete_poster( that );

			}else{
				alert('没有可删除的海报');
			}
			
		})
		.fail(function() {
			console.log("error");
		});

	});


});

function generate_poster( that ){

	$.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'generate_poster_task'},
	})
	.done(function( res ) {

		if( res.s == 200 ){

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {action: 'create-bigger-image', id: res.id, nonce: res.nonce },
			})
			.done(function( data ) {

				if( data.status == 200 ){
					that.data('progress', that.data('progress') * 1 + 1);

					that.text( that.data('progress') + ' / ' + that.data('count') );

					generate_poster( that );
				}
				
			})
			.fail(function() {
				alert("网络错误！");
			});

		}else{
			that.text('任务完成');
		}
		
	})
	.fail(function() {
		console.log("error");
	});
	

}

function delete_poster( that ){

	$.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: {action: 'del_poster_task'},
	})
	.done(function( res ) {

		if( res.s == 200 ){

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {action: 'del-poster', id: res.id },
			})
			.done(function( data ) {

				if( data.s == 200 ){
					that.data('progress', that.data('progress') * 1 + 1);

					that.text( that.data('progress') + ' / ' + that.data('count') );

					delete_poster( that );
				}
				
			})
			.fail(function() {
				alert("网络错误！");
			});

			

		}else{
			that.text('任务完成');
		}
		
	})
	.fail(function() {
		console.log("error");
	});
	

}



