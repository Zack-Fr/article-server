<?php
require_once("Model.php");

class Category extends Model{

    private int $id;

    private string $name;

    private string $content;

    protected static string $table = 'categories';

    public function __construct(array $data){
        $this->id = $data["id"];
        $this->name = $data["name"];
        $this->content = $data["content"];
    }
    public static function create(mysqli $mysqli, array $data){

        $sql='INSERT INTO ' . static::$table . ' (name, content)
        VALUE (?, ?)';

        $stmt = $mysqli->prepare($sql);
        if(!$stmt){
            throw new Exception('Prepare Failed:'.$mysqli->error);
        }
        $stmt->bind_param('ss',
            $data['name'],
            $data['content'],
        );
        if(!$stmt->execute()){
            throw new Exception('Execute Failed: '. $stmt->error);
        }
        return $mysqli->insert_id;
    }

    public function update(mysqli $mysqli): bool {

    $sql = "UPDATE " . static::$table . " SET name = ?, content = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssi", 
    $this->name, $this->content, $this->id);
    return $stmt->execute();
    }

    public static function deleteCategoryById(mysqli $mysqli, int $id): bool {

        // $sql = "Delete FROM " . static::$table . " WHERE id = ?"; //error 
        $sql = 'TRUNCATE TABLE . static::$table';
        $stmt = $mysqli->prepare($sql);

        if(! $stmt){
            error_log("MySQL prepare error: " . $mysqli->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        echo json_encode($sql);
        // $stmt->error;
        return $stmt->execute();
    }

    public static function deleteAllCategories(mysqli $mysqli, string $name, string $content): int {
        $sql = "DELETE FROM " . static::$table . 
        " WHERE name = ? AND content = ?";

        $stmt = $mysqli->prepare($sql);
        if(! $stmt){
            throw new Exception($mysqli->error);
        }
        $stmt->bind_param("ss", $name, $content);
        // echo json_encode($sql);
        if(! $stmt->execute()){
            throw new Exception($stmt->error);
        }
        $deleted = $stmt->affected_rows;
        $stmt->close();
        return $deleted;
    }
//==================================
    public function setName(string $name){
        $this->name = $name;
    }

    public function setContent(string $content){
        $this->content = $content;
    }
    public function toArray(){
        return [$this->id, $this->name, $this->content];
    }

    // public static function getCategories(mysqli $mysqli){
    //     $sql = 'SELECT DISTINCT category FROM ' . static::$table;
    //     $stmt = $mysqli->prepare($sql);
    //     if(! $stmt){
    //         error_log("MySQL prepare error: ". $mysqli->error);
    //         return false;
    //     }
    //     if (! $stmt->execute()) {
    //     error_log("MySQL execute error: " . $stmt->error);
    //     return [];
    //     }
    //     $result = $stmt->get_result();
    //     $categories =[];

    //     while($row = $result->fetch_assoc()){
    //         $categories[] =$row['category'];
    //     }
    //     $stmt->close();
        
    //     return $categories;
    // }


}