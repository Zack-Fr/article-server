<?php
require_once("Model.php");

class Article extends Model {

    private int $id; 
    private string $title;

    private string $author; 
    private string $description; 
    
    protected static string $table = 'articles';

    public function __construct(array $data){
        $this->id = $data["id"];
        $this->title = $data["title"];
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
    $stmt->bind_param("sssi", 
    $this->title, $this->author, $this->description, $this->id);
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
//==================================
    
    
    
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

    public function toArray(){
        return [$this->id, $this->title, $this->author, $this->description];
    }
    
}
