# A Functional, Elm-ish inspired WordPress Plugin

Thats a mouthful but I cant think of a better explanation for this plugin. Built around the PinkCrab Function Contrutors library and extended with a range of HTML functions. 

This is not a serious project and is more a bit of fun, in essence gets some quotes from a public api (well it will, when i look for one) and prints them either above or below the pages content. Has a meta box in wp-admin to toggle its use, the title and positioning.

Inspired loosely by Elm in regards the HTML syntax and how its used. Starts with a model which is passed through and update function before passing the populate model to a view function. 

```php
div(['id' => 'gin0115-quotes-metabox-post'])(
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
```
> The above renders a metabox form fields, interesting right?