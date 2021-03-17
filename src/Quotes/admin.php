<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Quotes\Admin;

use Gin0115\Functional_Plugin\Fixtures\Base_Model;
use function Gin0115\Functional_Plugin\Libs\HTML\Elements\{div, h2};
use function Gin0115\Functional_Plugin\Libs\HTML\Form\{input, select, label};
use PinkCrab\FunctionConstructors\{Arrays as Arr, GeneralFunctions as F, Comparisons as C};
use function Gin0115\Functional_Plugin\Libs\Utils\{dumper, arrayMapWith as mapWith, cloneWith, cloneWithMany, ifThen};

// Quote Meta Box Model
class Meta_Box_Model extends Base_Model {
	public int $postId = 0;
	public string $title = '';
	public string $showQuote = 'NO';
	public string $position = Quote_Position::BEFORE;
}

// Quote Position
/** @var array<BEFORE:string, AFTER:string> Constants*/
class Quote_Position {
	public const BEFORE = 'before_content';
	public const AFTER  = 'after_content';
}

// Quote Meta
/** @var array<DISPLAY:string, POSITION:string, TITLE:string>  Constants*/
class Quote_Meta_Keys {
	public const DISPLAY  = 'gin0115_fp_quote_display';
	public const POSITION = 'gin0115_fp_quote_position';
	public const TITLE    = 'gin0115_fp_quote_title';
    public const AS_LIST  = [ self::DISPLAY, self::POSITION, self::TITLE ];
}

/**
 * Updates the post meta on save.
 *
 * @param Meta_Box_Model $model The base model.
 * @param array<string, string> $post The Current global post state.
 * @return array
 */
function updateOnSavePost( Meta_Box_Model $model, array $post ) {
    return F\pipe        
        ( Arr\filterKey( C\isEqualIn(Quote_Meta_Keys::AS_LIST) )
        , Arr\map('sanitize_text_field') // Sanitize all values
        , fn($postMeta) =>  // Compile array of meta data and update in DB
            mapWith
                ( function($key, $value, $data){
                    return 
                        [ 'id' => $data[0]
                        , 'meta_key' => $key
                        , 'meta_value' => $value
                        , 'result' => update_post_meta( $data[0], $key, $value)
                        ];
                }, $postMeta, [get_the_ID()]
            )
        , Arr\groupBy(F\getProperty('meta_key')) // Group by meta key
        , Arr\map( F\getProperty('0')) // Flaten the nested array
        , F\recordEncoder // Update the model with the meta values.
            (clone $model)
            ( F\encodeProperty('title', F\pluckProperty
                ( Quote_Meta_Keys::TITLE
                , 'meta_value')
                )
            , F\encodeProperty('position', F\pluckProperty
                ( Quote_Meta_Keys::POSITION
                , 'meta_value')
                )
            , F\encodeProperty('showQuote', F\pluckProperty
                ( Quote_Meta_Keys::DISPLAY
                , 'meta_value')
                )
            )
        )
        ($post);
}

/**
 * Handles the update to the model on render meta box
 *
 * @param Meta_Box_Model $model
 * @param array $post_meta
 * @return void
 */
function updateModelRenderMetaBox( Meta_Box_Model $model, array $post_meta ) {
    return cloneWithMany
        ( $model
        , Quote_Meta_Keys::AS_LIST
        )
        (array_map('esc_attr', $post_meta));
}

/**
 * Renders the metabox view based on a passed model.
 *
 * @param Meta_Box_Model $model
 * @return string
 */
function view( Meta_Box_Model $model ): string {
	return div(['id' => 'gin0115-quotes-metabox-post'])
        ( h2( ['class' => 'meta_box_title'] )( 'Setup your quotes' )
			
        , div(['class' => 'form_field text'])
                ( label( Quote_Meta_Keys::TITLE )('Quote Block Title')
                , input
                    ( 'text'
                    , Quote_Meta_Keys::TITLE
                    )($model->title)
                )
            
        , div(['class' => 'form_field select'])
                ( label( Quote_Meta_Keys::DISPLAY )('Show quote on page')
                , select
                    ( Quote_Meta_Keys::DISPLAY 
                    , ['YES' => 'Yes', 'NO' => 'No']
                    )($model->showQuote)
                )

        , div(['class' => 'form_field select'])
                ( label( Quote_Meta_Keys::POSITION )('Quote position')
                , select
                    ( Quote_Meta_Keys::POSITION 
                    , [ Quote_Position::BEFORE => 'Before main content', Quote_Position::AFTER => 'After main content']
                    )($model->position)
                )
		);
}

