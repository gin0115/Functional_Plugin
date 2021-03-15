<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Meta_Box;

use Gin0115\Functional_Plugin\Fixtures\Base_Model;
use PinkCrab\FunctionConstructors\{Arrays as Arr};
use function Gin0115\Functional_Plugin\HTML\Elements\{div, h2, p};
use function Gin0115\Functional_Plugin\HTML\Form\{input, select, label};

// Quote Meta Box Model
class Meta_Box_Model extends Base_Model {
	public int $post_id;
	public string $title;
	public bool $show_quote = false;
	public string $position = Quote_Position::BEFORE;
}

// Quote Position TypeAlias
class Quote_Position {
	public const BEFORE = 'before_content';
	public const AFTER  = 'after_content';
}

// Holds all the quote meta keys
class Quote_Meta_Keys {
	public const DISPLAY  = 'gin0115_fp_quote_display';
	public const POSITION = 'gin0115_fp_quote_position';
	public const TITLE    = 'gin0115_fp_quote_title';
}

/**
 * Handles all the changes to the model.
 *
 * @param Meta_Box_Model $model The base model.
 * @param array<string, string> $post The Current global post state.
 * @return Meta_Box_Model
 */
function update( Meta_Box_Model $model, array $post ): Meta_Box_Model {
	return $model;
}

/**
 * Renders the metabox view based on a passed model.
 *
 * @param Meta_Box_Model $model
 * @return string
 */
function view( Meta_Box_Model $model ): string {
	return div(['id' => 'gin0115-quotes-metabox-post'])(
        Arr\toString(PHP_EOL)(
			[ h2( ['class' => 'meta_box_title'] )( 'Setup your quotes' )
			
            , div(['class' => 'form_field text'])
                (label( Quote_Meta_Keys::TITLE )('Quote Block Title')
                , input('text', Quote_Meta_Keys::TITLE)($model->title)
                )
            
            , div(['class' => 'form_field checkbox'])
                (label( Quote_Meta_Keys::DISPLAY )('Show quote on page')
                , input('checkbox', Quote_Meta_Keys::DISPLAY)($model->show_quote ? 'YES' : 'NO')
                )
            
            , div(['class' => 'form_field select'])
                (label( Quote_Meta_Keys::POSITION )('Quote position')
                ,select( Quote_Meta_Keys::POSITION , _meta_box_postion_options())($model->position)
                )
            ]
		)
    );
}

/**
 * Returns the postion selection options array.
 *
 * @return array<string, string>
 */
 function _meta_box_postion_options(): array
{
    return [ 
        Quote_Position::BEFORE => 'Before main content', 
        Quote_Position::AFTER => 'After main content' 
    ];
}
