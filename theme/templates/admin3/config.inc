<?php

$user = 'brian';
$pass = 'megabates2';

try {
    $conn = new PDO('mysql:host=localhost;dbname=work', $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}


