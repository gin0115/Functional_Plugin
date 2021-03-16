<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Meta_Box;

use Gin0115\Functional_Plugin\Fixtures\Base_Model;
use function Gin0115\Functional_Plugin\HTML\Elements\{div, h2};
use function Gin0115\Functional_Plugin\Libs\Records\{model_with};
use function Gin0115\Functional_Plugin\HTML\Form\{input, select, label};
use function Gin0115\Functional_Plugin\Libs\Utils\{arrayMapWith as mapWith, ifThen};
use PinkCrab\FunctionConstructors\{Arrays as Arr, GeneralFunctions as F, Comparisons as C};

// Quote Meta Box Model
class Meta_Box_Model extends Base_Model {
	public int $post_id;
	public string $title;
	public string $show_quote = 'NO';
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
}

/**
 * Updates the post meta on save.
 *
 * @param Meta_Box_Model $model The base model.
 * @param array<string, string> $post The Current global post state.
 * @return array
 */
function update_on_save( Meta_Box_Model $model, array $post ) {
    return F\pipe        
        ( Arr\filterKey // Filter out the needed keys from the pased array
            ( C\isEqualIn(
                [ Quote_Meta_Keys::DISPLAY
                , Quote_Meta_Keys::POSITION
                , Quote_Meta_Keys::TITLE
                ]
            ))
        , Arr\map('sanitize_text_field') // Sanitize all values
        , fn($e) =>  // Compile array of meta data and update in DB
            mapWith
                ( function($key, $value, $data){
                    return 
                        [ 'id' => $data[0]
                        , 'meta_key' => $key
                        , 'meta_value' => $value
                        , 'result' => update_post_meta( $data[0], $key, $value)
                        ];
                }, $e, [get_the_ID()]
            )
        , fn($e) => // Extrct just key and value
            mapWith
            ( function($key, $meta){
                return [$meta['meta_key'] => $meta['meta_value']];
            }, $e
            )
        , Arr\groupBy(fn($e)=> array_keys($e)[0] ) // Group by meta key
        , Arr\map( F\pipe(Arr\flattenByN(2), F\getProperty('0'))) // Flaten the nested array
        , function($meta) use ($model) : Meta_Box_Model { // Update the model

               $model =  ifThen
                    ( F\hasProperty(Quote_Meta_Keys::TITLE)
                    , fn($e)=>  F\recordEncoder(clone $model)(_encoder('title', Quote_Meta_Keys::TITLE))($e)
                    , $model
                    )
                    ($meta);

                $model =  ifThen
                    ( F\hasProperty(Quote_Meta_Keys::POSITION)
                    , fn($e)=>  F\recordEncoder(clone $model)(_encoder('position', Quote_Meta_Keys::POSITION))($e)
                    , $model
                    )
                    ($meta);

                $model =  ifThen
                    ( F\hasProperty(Quote_Meta_Keys::DISPLAY)
                    , fn($e)=>  F\recordEncoder(clone $model)(_encoder('show_quote', Quote_Meta_Keys::DISPLAY))($e)
                    , $model
                    )
                    ($meta);
                
                return $model;
            }
        )
        ($post);
}

/**
 * Alias for recordEncoder using getProperty to create a callable.
 *
 * @param string $to_set
 * @param string $from
 * @return callable
 */
function _encoder(string $to_set, string $from): callable
{
    return F\encodeProperty($to_set, F\getProperty($from));
}

/**
 * Handles the update to the model on render meta box
 *
 * @param Meta_Box_Model $model
 * @param array $post_meta
 * @return void
 */
function update_on_render_meta_box( Meta_Box_Model $model, array $post_meta ) {
    return model_with
        ( $model
        , array_map('esc_attr', $post_meta)
        );
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
                    )($model->show_quote)
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

