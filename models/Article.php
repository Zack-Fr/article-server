<?php
require_once("Model.php");

class Article extends Model {

    private int $id; 
    private string $title;
    
    private string $category;

    private string $author; 
    private string $description; 
    
    protected static string $table = 'articles';

    public function __construct(array $data){
        $this->id = $data["id"];
        $this->title = $data["title"];
        $this->category = $data["category"];
        $this->author = $data["author"];
        $this->description = $data["description"];
    }
    public static function createArticle(mysqli $mysqli, array $data) 
    {
        $sql = '
        INSERT INTO ' . static::$table .'
                (title, category, author, description)
        VALUES(?, ?, ?, ?)
        ';

        $stmt = $mysqli->prepare($sql);
        if(!$stmt){
            throw new Exception('Prepare Failed:' . $mysqli->error);
        }

        $stmt->bind_param(
            'ssss',
            $data['title'],
            $data['category'],
            $data['author'],
            $data['description'],
        ); 
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        return $mysqli->insert_id;
    }
    public function update(mysqli $mysqli): bool {

    $sql = "UPDATE " . static::$table . " SET title = ?, category = ?, author = ?, description = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssi", 
    $this->title, $this->category, $this->author, $this->description, $this->id);
    return $stmt->execute();
    }
    public static function deleteAllArticles(mysqli $mysqli): bool {
        $sql = 'TRUNCATE TABLE ' . static::$table;//error caused as to a space between concatenated string I needed to add a space before the single quote
        $stmt = $mysqli->prepare($sql);
        echo json_encode($sql);
        return $stmt->execute();
    }
    public static function deleteArticleById(mysqli $mysqli, int $id): bool {

        $sql = "Delete FROM " . static::$table . " WHERE id = ?"; //error 
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
    public static function getCategories(mysqli $mysqli){
        $sql = 'SELECT DISTINCT category FROM ' . static::$table;
        $stmt = $mysqli->prepare($sql);
        if(! $stmt){
            error_log("MySQL prepare error: ". $mysqli->error);
            return false;
        }
        if (! $stmt->execute()) {
        error_log("MySQL execute error: " . $stmt->error);
        return [];
        }
        $result = $stmt->get_result();
        $categories =[];

        while($row = $result->fetch_assoc()){
            $categories[] =$row['category'];
        }
        $stmt->close();
        
        return $categories;
    }
    /**
     * Summary of getCategoriesById
     * @param mysqli $mysqli
     * @param int[] $ids
     * @return string[] array of categories
     */
    public static function getCategoriesById(mysqli $mysqli, array $ids){
        $ids = array_map('intval', $ids);//extract the integers from the request 
        if(count($ids)=== 0){
            return [];
        }
        //a placeholder for the ids array list
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT category FROM " . static::$table . " WHERE id IN ($placeholders)";

        $stmt = $mysqli->prepare($sql);
        if(! $stmt){
            error_log("MySQL prepare error: ". $mysqli->error);
            return false;
        }
        //bind all ids
        $types = str_repeat('i', count($ids));
        //bind mysqli for each id
        $refs = [];
        $refs [] = & $types;
        foreach($ids as $i => $val){
            $refs[] = & $ids[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
        
        if (! $stmt->execute()) {
        error_log("MySQL execute error: " . $stmt->error);
        return [];
        }
        
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()){
            $categories[] = $row['category'];
        }
        
        $stmt->close();
        return $categories;
    }
    
    public static function addNewCategory(mysqli $mysqli, array $data): int|string{
        require(__DIR__ . "/../connection/connection.php");

        $sql= "INSERT INTO" . static::$table . 
        "(id, category)
        VALUE(?, ?)";

        $stmt = $mysqli->prepare($sql);
        if(! $stmt){
            throw new Exception('preparing the SQl Failed: ' . $stmt->error);
        }

        $stmt->bind_param('is',
                $data['id'],
                // $data['title'],
                $data['category'],
                // $data['author'],
                // $data['description'],
        );

        if(! $stmt->execute()){
            throw new Exception('Execution of the query failed '. $stmt->error);
        }
        return $mysqli->insert_id;
        
    }
        




    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getDescription(): string {
        return $this->description;
    }
    
    public function setTitle(string $title){
        $this->title = $title;
    }

    public function setAuthor(string $author){
        $this->author = $author;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }

    // public function getAllArticles(string)

    public function toArray(){
        return [$this->id, $this->title, $this->category, $this->author, $this->description];
    }
    
}
