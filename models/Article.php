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
