<?php

/**
 * Entry point for the application.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin;
use function Gin0115\Functional_Plugin\Libs\Records\{model_with};
use Gin0115\Functional_Plugin\Meta_Box\{Meta_Box_Model,Quote_Meta_Keys};
use function Gin0115\Functional_Plugin\Meta_Box\{view, update_on_render_meta_box, update_on_save};

function main(): void {

	$get_meta = function($post_id){
		return 
			[ 'post_id'    => $post_id
			, 'show_quote' => get_post_meta( $post_id, Quote_Meta_Keys::DISPLAY, true )
			, 'position'   => get_post_meta( $post_id, Quote_Meta_Keys::POSITION, true )
			, 'title'      => get_post_meta( $post_id, Quote_Meta_Keys::TITLE, true )
			];
	};

	// This is a mess and im not happy with it, but i'll get round to it.
	add_action('add_meta_boxes', function() use ($get_meta){		
		add_meta_box
			( 'gin0115_fp_page_meta_box'
			, 'Quotes'
			, fn() => print view( update_on_render_meta_box
				( new Meta_Box_Model()
				, $get_meta(\get_the_ID())
				))
			, array( 'page' ) 
			);
	});

	// Save page hook.
	add_action('save_post_page', function(int $post_id) use ($get_meta){
		update_on_save
			( model_with( new Meta_Box_Model(), $get_meta($post_id))
			, array_map('sanitize_text_field', $_POST ?? [])
			);
	});
}
