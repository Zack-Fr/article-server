
<?php 
require __DIR__ .'/../connection/connection.php';
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

//Routing starts here (Mapping between the request and the controller & method names)
//It's an key-value array where the value is an key-value array
//=========================articles
$apis = [
    '/get_articles'         => ['controller' => 'ArticleController', 'method' => 'getAllArticles'],

    '/delete_articles'         => ['controller' => 'ArticleController', 'method' => 'deleteAllArticles'],

    '/delete_article_byId'         => ['controller' => 'ArticleController', 'method' => 'deleteById'],

    '/add_article'         => ['controller' => 'ArticleController', 'method' => 'addArticle'],

    '/update_article'         => ['controller' => 'ArticleController', 'method' => 'updateArticle'],

    '/get_article_id'         => ['controller' => 'ArticleController', 'method' => 'getArticleById'],
//================================categories
    '/get_categories'         => ['controller' => 'CategoryController', 'method' => 'getCategories'],

    '/update_category'         => ['controller' => 'CategoryController', 'method' => 'updateCategory'],

    '/delete_category_by_id'         => ['controller' => 'CategoryController', 'method' => 'deleteCategoryById'],

    '/delete_all_categories'         => ['controller' => 'CategoryController', 'method' => 'deleteAllCategories'],
    
    '/add_new_category'         => ['controller' => 'CategoryController', 'method' => 'addNewCategory'],

//===============================user

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
    // echo $controller_name;
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