<?php
require_once("Model.php");

class User extends Model{

    private int $id; 
    
    protected static string $table = "users";

    public function __construct(array $data){
        $this->id = $data["id"];
    }

    public function toArray(){
        return [$this->id];
    }
}
