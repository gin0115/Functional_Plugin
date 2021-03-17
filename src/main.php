<?php

/**
 * Entry point for the application.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin;
use function Gin0115\Functional_Plugin\Libs\Utils\{cloneWithMany};
use Gin0115\Functional_Plugin\Quotes\Admin\{Meta_Box_Model,Quote_Meta_Keys};
use function Gin0115\Functional_Plugin\Quotes\Admin\{view, updateModelRenderMetaBox, updateOnSavePost};

function main(): void {

	/**
	 * Creates a function for getting all post meta, in a formatted array
	 * based on the post id.
	 * 
	 * @param int $postId
	 * @return array{postId:int, shq}
	 */
	$getMeta = function($postId){
		return 
			[ 'postId'    => $postId
			, 'showQuote' => get_post_meta( $postId, Quote_Meta_Keys::DISPLAY, true )
			, 'position'   => get_post_meta( $postId, Quote_Meta_Keys::POSITION, true )
			, 'title'      => get_post_meta( $postId, Quote_Meta_Keys::TITLE, true )
			];
	};

	// This is a mess and im not happy with it, but i'll get round to it.
	add_action('add_meta_boxes', function() use ($getMeta){		
		add_meta_box
			( 'gin0115_fp_page_meta_box'
			, 'Quotes'
			, fn() => print view
				( updateModelRenderMetaBox
					( new Meta_Box_Model()
					, $getMeta( (int) \get_the_ID() )
					)
				)
			, array( 'page' ) 
			);
	});

	// Save page hook.
	add_action('save_post_page', function($postId) use ($getMeta){
		updateOnSavePost
			( cloneWithMany
				( new Meta_Box_Model()
				, Quote_Meta_Keys::AS_LIST)
				( $getMeta($postId)
				)
			, array_map('sanitize_text_field', $_POST ?? [])
			);
	});
}
