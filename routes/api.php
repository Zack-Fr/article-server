
<?php 
require __DIR__ .'/../config/connection.php';
// This block is used to extract the route name from the URL
//----------------------------------------------------------
// Define your base directory 
$base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base directory from the request if present
if (strpos($request, $base_dir) === 0) {
    $request = substr($request, strlen($base_dir));
}

// Ensure the request is at least '/'
if ($request == '') {
    $request = '/';
}
$method = $_SERVER['REQUEST_METHOD'];
//Examples: 
//http://localhost/getArticles -------> $request = "getArticles"
//http://localhost/ -------> $request = "/" (why? because of the if)

// This block is used to extract the route name from the URL
//----------------------------------------------------------

switch ("$method $request"){
//Methods and apis
//--------------- Articles Api's
    case 'GET /articles':
        require __DIR__. '/../controllers/ArticleController.php';
    case 'POST /delete_articles':
        require __DIR__. '/../controllers/ArticleController.php';
//--------------- User Api's
    case 'POST /login':
        require __DIR__. '/../controllers/UserController.php';
    case 'POST /register':
        require __DIR__. '/../controllers/UserController.php';
    }

    
//Routing starts here (Mapping between the request and the controller & method names)
//It's an key-value array where the value is an key-value array
//----------------------------------------------------------
/* The `` array is serving as a routing table in the PHP code. It maps specific URL routes to
corresponding controller and method names. */
$apis = [
    '/articles'         => ['controller' => 'ArticleController', 'method' => 'getAllArticles'],
    '/delete_articles'         => ['controller' => 'ArticleController', 'method' => 'deleteAllArticles'],

    '/login'         => ['controller' => 'AuthController', 'method' => 'login'],
    '/register'         => ['controller' => 'AuthController', 'method' => 'register'],

];

//----------------------------------------------------------


//Routing Logic here 
//This is a dynamic logic, that works on any array... 
//----------------------------------------------------------
if (isset($apis[$request])) {
    $controller_name = $apis[$request]['controller']; //if $request == /articles, then the $controller_name will be "ArticleController" 
    $method = $apis[$request]['method'];
    require_once "controllers/{$controller_name}.php";

    $controller = new $controller_name();
    if (method_exists($controller, $method)) {
        $controller->$method();
    } else {
        echo "Error: Method {$method} not found in {$controller_name}.";
    }
} else {
    echo "404 Not Found";
    echo "$request";
}