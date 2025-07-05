<?php 

require(__DIR__ . "/../models/Article.php");
require(__DIR__ . "/../connection/connection.php");
require(__DIR__ . "/../services/ArticleService.php");
require(__DIR__ . "/../services/ResponseService.php");
// $response =[];
// $response["status"]=200;


class ArticleController{
    
    public function getAllArticles(){
        global $mysqli;

        if(!isset($_GET["id"])){
            $articles = Article::all($mysqli);
            $articles_array = ArticleService::articlesToArray($articles); 
            echo ResponseService::success_response($articles_array);
            return;
        }

        $id = $_GET["id"];
        $article = Article::find($mysqli, $id)->toArray();
        echo ResponseService::success_response($article);
        return;
    }

    
    public function getCategories()
    {
        header('Content-Type: application/json');
        require(__DIR__ . "/../connection/connection.php");
        
        $categories = Article::getCategories($mysqli);
        echo ResponseService::success_response($categories);
        exit;
    }
    
    public function getCategoriesById()
    {
        header('Content-Type: application/json');
        require(__DIR__ . "/../connection/connection.php");

        //accept id or ids
        $ids = $_GET['id']  ?? $_GET['ids'] ?? [];

        if(!isset($_GET["id"])){
            echo ResponseService::error_response('Missing id');
            exit;
        }
        //accept ?id=2,3... and turn it to array
        if(! is_array($ids)){
            $ids = explode(',',(string)$ids);
        }
        
        $categories = Article::getCategoriesById($mysqli, $ids);
        // echo ('what the hell');
        
        if ($categories === null){
            echo ResponseService::error_response('No category found for this article id');
        } else{
            echo ResponseService::success_response($categories);
            exit;
        }
    }
    public static function addNewCategory(){
        require(__DIR__ . "/../connection/connection.php");

        
        $newCategory = Article::addNewCategory($mysqli,$data);
        echo json_encode([$data]);
    }
    
    public function addArticle() {
        $input = json_decode(file_get_contents('php://input'), true);
        $title       = trim($input['title']       ?? '');
        $category       = trim($input['category']       ?? '');
        $author       = trim($input['author']       ?? '');
        $description       = trim($input['description']       ?? '');
        
        // require(__DIR__ . "/../models/Article.php"); caused class duplication error
        require(__DIR__ . "/../connection/connection.php");
        
        try {
            $newId = Article::createArticle($mysqli, [
                'title' => $title,
                'category'=>$category,
                'author'=>$author,
                'description' =>$description 
            ]);
            
            http_response_code(201);
            echo json_encode([
                'status'   => 'success',
                'movie_id' => $newId,
                'message'  => 'Film added successfully'
            ]);
            
        } catch (Exception $e){
            http_response_code(500);
            echo json_encode([
                'error'   => 'Could not add article',
                'details' => $e->getMessage()
            ]);
        }
    }
    public function getArticleById() {
        // session_start();
        // header('Content-Type: application/json');
        require(__DIR__ . "/../connection/connection.php");
        
        $response = [];
        $response ["status"]=200;
        
        if(!isset($_GET["id"])){
            $movies = Article::all($mysqli);
            $response["articles"] = [];
            
            foreach($movies as $m){
                $response["articles"][] =$m->toArray();
            }
            echo json_encode($response);
            return;
        }
        $id = $_GET["id"];
        $article = Article::find($mysqli,$id);
        $response["articles"] = $article->toArray();
        
        echo json_encode($response);
        return;
    }
    public function updateArticle()
    {
        require __DIR__ . '/../connection/connection.php';
        session_start();
        header('Content-Type: application/json');
        
        
        if (!isset($_GET['id'])) {
            $all = Article::all($mysqli);
            echo json_encode([
                'status'   => 200,
                'articles' => array_map(fn($a) => $a->toArray(), $all),
            ]);
            exit;
        }
        
        $id      = (int) $_GET['id'];
        $article = Article::find($mysqli, $id);
        
        if (! $article) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
            exit;
        }
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // fields
            if (isset($input['title'])) {
                $article->setAuthor($input['title']);
            }
            if (isset($input['category'])) {
                $article->setTitle($input['category']);
            }
            if (isset($input['author'])) {
                $article->setTitle($input['author']);
            }
            if (isset($input['description'])) {
                $article->setTitle($input['description']);
            }
            
            
            if (! $article->update($mysqli)) {
                http_response_code(500);
                echo json_encode(['error' => 'Could not update article']);
                exit;
            }
        }
        //     echo json_encode([
            //         'status'  => 200,
            //         'article' => $article->toArray(),
            // ]);
            exit;
    }
    public function deleteAllArticles(){
            require(__DIR__ . "/../connection/connection.php");
            try{
                $deleteArticles = Article::deleteAllArticles($mysqli);
            }
            catch (Exception $e){
                    http_response_code(500);
                    echo json_encode([
                    'error'   => '',
                    'details' => $e->getMessage()
                    ]);
            }
            die("Deleting...");
    }
    
    public function deleteArticleById()
    {
        header('Content-Type: application/json');
    
        $id = null;
        if (isset($_GET['id']) && trim($_GET['id']) !== '') {
            $id = (int) $_GET['id'];
        } elseif (isset($_POST['id']) && trim($_POST['id']) !== '') {
            $id = (int) $_POST['id'];
        }
    
        require __DIR__ . '/../connection/connection.php';
        try {
            $deleted = Article::deleteArticleById($mysqli, $id);
            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => "Article $id deleted."
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error'   => 'Could not delete article'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => 'Exception thrown',
                'details' => $e->getMessage()
            ]);
        }
    
        exit;
    }
    }
    
    //To-Do:
    
    //1- Try/Catch in controllers ONLY!!! 
    //2- Find a way to remove the hard coded response code (from ResponseService.php)
    //3- Include the routes file (api.php) in the (index.php) -- In other words, seperate the routing from the index (which is the engine)
    //4- Create a BaseController and clean some imports 