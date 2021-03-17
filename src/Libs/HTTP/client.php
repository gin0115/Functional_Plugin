<?php

/**
 * HTTP client.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Libs\HTTP;

use stdClass;
use Gin0115\Functional_Plugin\Fixtures\Base_Model;
use PinkCrab\FunctionConstructors\{GeneralFunctions as F, Srings as Str, Comparisons as C};
use function Gin0115\Functional_Plugin\Libs\Utils\{ifElse, passThrough, setPropertyWith, dumper, cloneWith};

const METHOD_GET = 'GET';
const METHOD_POST = 'POST';

/**
 * Model for request
 * @var Base_Model
 */
class Request extends Base_Model {
    public ?Request_Args $args = null;
    public string $url = '';
    public string $error = '';
}

/**
 * Model for response
 * @var Base_Model
 */
class Response extends Base_Model {
    public int $code = 0;
    public array $headers = [];
    public array $body = [];
    public array $raw = [];
    public string $error = '';
}

/**
 * Request args.
 * @var Base_Model
 */
class Request_Args extends stdClass{
    public string $method = METHOD_GET;
    public float $timeout = 5;
    public int $redirection = 5;
    public array $headers = [];
    public array $cookies = [];
    public ?array $body = null;    
}

/**
 * Does a request based on a request object passed.
 * Returns a callable which takes a url.
 * URL can be defined in the request, in which case pass an empty string to returned callable
 *
 * @param Request $request
 * @return callable(string):array<string, mixed>|WP_Error
 */
function request(Request $request): callable {
    return function(string $url = '') use ($request){
       
       // Add the url to the request, if its been set.
        $request = ifElse
            ( F\pipe('strlen', C\isNotEqualTo(0))
            , cloneWith($request, 'url')
            , F\always(cloneWith($request, 'error')('Missing URL'))// Returns the model with a 
            )($url);

        $response = ifElse
            ( F\pipe
                ( F\getProperty('error')
                , 'strlen'
                , C\isEqualTo(0)
                )
            // If we have no errors, do the api call and populate the 
            ,'dump'
            // If we had an error, pass the error mesage to the responce.
            , F\pipeR(setPropertyWith(new Response, 'error'), F\getProperty('error')) 
            );
            
        dump($request);
        return (new \WP_Http)->request
            ( esc_url($request->url)
            , F\toArray()($request->request_args)
            );
    };
}