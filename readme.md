# A Functional PHP, Elm-ish inspired WordPress Plugin

Thats a mouthful but I cant think of a better explanation for this plugin. Built around the PinkCrab Function Contrutors library and extended with a range of HTML functions. 

This is not a serious project and is more a bit of fun, in essence gets some quotes from a public api (well it will, when i look for one) and prints them either above or below the pages content. Has a meta box in wp-admin to toggle its use, the title and positioning.

Inspired loosely by Elm in regards the HTML syntax and how its used. Starts with a model which is passed through and update function before passing the populate model to a view function. 

```php
div(['id' => 'gin0115-quotes-metabox-post'])
    ( h2( ['class' => 'meta_box_title'] )( 'Setup your quotes' )
        
    , div(['class' => 'form_field text'])
        ( label( Quote_Meta_Keys::TITLE )('Quote Block Title')
        , input( 'text', Quote_Meta_Keys::TITLE)($model->title)
        )
        
    , div(['class' => 'form_field select'])
        ( label( Quote_Meta_Keys::DISPLAY )('Show quote on page')
        , select( Quote_Meta_Keys::DISPLAY, ['YES' => 'Yes', 'NO' => 'No'])
            ($model->showQuote)
        )

    , div(['class' => 'form_field select'])
        ( label( Quote_Meta_Keys::POSITION )('Quote position')
        , select
            ( Quote_Meta_Keys::POSITION 
            , [ Quote_Position::BEFORE => 'Before main content'
              , Quote_Position::AFTER => 'After main content'
              ]
            )($model->position)
        )
    );
```
> The above renders a metabox form fields, interesting right?

## HTML

You can render several HTML elements

### div([attributes])(...children): string(html)
```php
use function Gin0115\Functional_Plugin\Libs\HTML\Elements\{div,p....}
print div(['id'=>'parent_container', 'class'=>'container', 'data-foo' => 'bar'])
    ( p(['class'=>'child'])('Child 1')
    , p(['class'=>'child'])('Child 2')
    , p(['class'=>'child'])('Child 2')        
    );

// <div id="parent_container" class="container" data-foo="bar">
// <p class="child">Child 1</p>
// <p class="child">Child 2</p>
// <p class="child">Child 3</p>
// </div>

// Using partial application.
use PinkCrab\FunctionConstructors\{Arrays as Arr};
print div( ['id'=>'parent_container', 'class'=>'container', 'data-foo' => 'bar'] )
    ( ...Arr\map( p( ['class'=>'child'] ) ) // Wrap each as p tags
      ( ['Child 1', 'Child 2', 'Child 3'] ) // The contents .
    );

// Can be made more compact, but hard to read
print div( ['id'=>'parent_container', 'class'=>'container', 'data-foo' => 'bar'] )
    ( ...Arr\map( p( ['class'=>'child'] ) )( ['Child 1', 'Child 2', 'Child 3'] ) );
```
> The P, Span, H2 all work in the same fasion.

### img div([attributes])(void)
Unlike the other elements, the img tag has not children and as a result the return function does nothing with the (child) values passed.
```php
use function Gin0115\Functional_Plugin\Libs\HTML\Elements\{img}
print img(['src' => 'http://somewhere', 'FLAG' => null])();
// <img src="http://somewhere" FLAG>
```
This isnt ideal for mapping arrays of image urls, what you can do here is create your own function.
```php
$img = function(array $attributes = []): callable {
    return function(string $src) use ($attributes){
        $attributes['src'] = $src;
        return img($attributes)();
    }
}

$images = array_map($img(['class'=>'image']), ['array of image urls']);
// ['<img src="image_url1" class="image">'.......]

// You can then pass it into a div
print div(['class'=>'image_wrapper'])(...$images);
```
### ifThen(callable, callable, mixed)

Allows for the creation of simple ifThen statements, will return the 3rd param id the initial conditional fails.
```php
use function Gin0115\Functional_Plugin\Libs\HTML\Elements\{ifThen};

$value = ifThen('is_string', 'strtolower', false);

print $value('HELLO'); // hello
print $value(['array']); // false
```

### ifElse(callable, callable, callable)

Allows the creation of a simple if else statment. The first argument is the conditional, followed by the true and then false callbacks.
```php
use function Gin0115\Functional_Plugin\Libs\HTML\Elements\{ifElse};
use function PinkCrab\FunctionConstructors\GeneralFunctions as F;

$even = ifElse(fn($e) => $e % 2 === 0, F\always('Is Even'), F\always('Is False'));
print $even(222); // Is Even
print $even(777); // If False
```