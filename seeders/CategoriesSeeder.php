<?php
class CategoriesSeeder {

    private mysqli $mysqli;
    private string $dataFile;

    public function __construct(mysqli $mysqli, string $dataFile){
        $this->mysqli = $mysqli;
        $this->dataFile = $dataFile;
    }

    public function run(): void
    {
                if (!file_exists($this->dataFile)) {
            throw new Exception("Seed file not found: {$this->dataFile}");
        }
        require("../connection/connection.php");
    }

}