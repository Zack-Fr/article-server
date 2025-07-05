<?php 

require(__DIR__ . "/../models/Category.php");
require(__DIR__ . "/../connection/connection.php");
require(__DIR__ . "/../services/CategoryService.php");
require(__DIR__ . "/../services/ResponseService.php");
// $response =[];
// $response["status"]=200;

class CategoryController {

    public function getCategories()
    {
        header('Content-Type: application/json');
        global $mysqli;
        require(__DIR__ . "/../connection/connection.php");
        
        if(!isset($_GET["id"])){
            $categories = Category::all($mysqli);
            $categories_array = CategoryService::categoriesToArray($categories); 
            echo ResponseService::success_response($categories_array);
            return;
        }

            $id= $_GET["id"];
            $categories = Category::find($mysqli, $id)->toArray();
            echo ResponseService::success_response($categories);
            return;
    }

    // public function getCategoriesById()
    // {
    //     header('Content-Type: application/json');
    //     require(__DIR__ . "/../connection/connection.php");

    //     //accept id or ids
    //     $ids = $_GET['id']  ?? $_GET['ids'] ?? [];

    //     if(!isset($_GET["id"])){
    //         echo ResponseService::error_response('Missing id');
    //         exit;
    //     }
    //     //accept ?id=2,3... and turn it to array
    //     if(! is_array($ids)){
    //         $ids = explode(',',(string)$ids);
    //     }
        
    //     $categories = Article::getCategoriesById($mysqli, $ids);
    //     // echo ('what the hell');
        
    //     if ($categories === null){
    //         echo ResponseService::error_response('No category found for this article id');
    //     } else{
    //         echo ResponseService::success_response($categories);
    //         exit;
    //     }
    // }

    public static function addNewCategory(){

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $name =trim($input['name'] ?? '');
        $content =trim($input['content'] ?? '');
        
        require(__DIR__ . "/../connection/connection.php");
        
        if($name === ''){
            ResponseService::error_response('`name`  is required');
            exit;
        }
        try{
            $newId = Category::create($mysqli, [
            'name' => $name,
            'content'=>$content,
        ]);
        echo ResponseService::success_response([
            'id' => $newId,
            'message' => 'Category added'
        ]);
            } catch (Exception $e){
                http_response_code(500);
                ResponseService::error_response(['Could not add category', 'details'=> $e->getMessage()]);
            }
    }

    public function updateCategory()
    {
        require __DIR__ . '/../connection/connection.php';
        session_start();
        header('Content-Type: application/json');
        
        
        if (!isset($_GET['id'])) {
            $all = Category::all($mysqli);
            echo json_encode([
                'status'   => 200,
                'articles' => array_map(fn($a) => $a->toArray(), $all),
            ]);
            exit;
        }
        
        $id      = (int) $_GET['id'];
        $category = Category::find($mysqli, $id);
        
        if (! $category) {
            http_response_code(404);
            echo json_encode(['error' => 'Article not found']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // fields
            if (isset($input['name'])) {
                $category->setName($input['name']);
            }
            if (isset($input['content'])) {
                $category->setContent($input['content']);
            }
            }
            if (! $category->update($mysqli)) {
                http_response_code(500);
                echo json_encode(['error' => 'Could not update article']);
                exit;
            }
                        echo json_encode([
                    'status'  => 200,
                    'category' => $category->toArray(),
            ]);
            exit;
        }
}