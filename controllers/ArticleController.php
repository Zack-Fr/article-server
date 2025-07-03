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

    public function deleteAllArticles(){
        die("Deleting...");
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

        } catch (Exception $e){
                http_response_code(500);
                echo json_encode([
                'error'   => 'Could not add article',
                'details' => $e->getMessage()
                ]);
            }
    }

}

//To-Do:

//1- Try/Catch in controllers ONLY!!! 
//2- Find a way to remove the hard coded response code (from ResponseService.php)
//3- Include the routes file (api.php) in the (index.php) -- In other words, seperate the routing from the index (which is the engine)
//4- Create a BaseController and clean some imports 