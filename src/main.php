<?php

/**
 * Entry point for the application.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin;
use function Gin0115\Functional_Plugin\Meta_Box\{view};
use function Gin0115\Functional_Plugin\Libs\Records\{model_with};
use Gin0115\Functional_Plugin\Meta_Box\{Meta_Box_Model,Quote_Meta_Keys};

function main(): void {

	// This is a mess and im not happy with it, but i'll get round to it.
	add_action(
		'add_meta_boxes',
		function() {

			$model = model_with(
				new Meta_Box_Model(),
				array(
					'post_id'    => \get_the_ID(),
					'show_quote' => get_post_meta( \get_the_ID(), Quote_Meta_Keys::DISPLAY, true ),
					'position'   => get_post_meta( \get_the_ID(), Quote_Meta_Keys::POSITION, true ),
					'title'      => get_post_meta( \get_the_ID(), Quote_Meta_Keys::TITLE, true ),
				)
			);

		add_meta_box( 
			'gin0115_fp_page_meta_box', 
			'Quotes', 
			fn() => print view( $model ), 
			array( 'page' ) 
		);

dump(view( $model ));
			dump( array( $model, view( $model ) ) );
            print view( $model );
		}
	);

}
