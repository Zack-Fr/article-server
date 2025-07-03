<?php

// echo json_encode($_SERVER['HTTP_ORIGIN']);
if (isset($_SERVER['HTTP_ORIGIN'])) {
  // echo back the exact origin instead of '*'
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

//preflight
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    exit(0);
}

$path = $_SERVER['PATH_INFO'] ?? '' ; 
if($path){
    $_SERVER['REQUEST_URI'] = $path;
}

require __DIR__. '/../article-server-main/routes/api.php';
