<?php


//$host = 'localhost';
//$dbname = 'work';
$user = 'bb9b0de8b99a46';
$pass = '4fc57a4d';

try {
    $conn = new PDO('mysql:host=us-cdbr-iron-east-01.cleardb.net;dbname=heroku_635bec65ba648c2', $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}

 