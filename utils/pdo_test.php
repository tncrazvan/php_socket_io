
<?php
$dsn = 'mysql:host=localhost;dbname=test';
$username = 'root';
$password = 'root';

$pdo = new PDO($dsn, $username, $password);

$stmt=$pdo->prepare("select * from test.test_table");
$result=$stmt->fetchAll();
echo "\n";
print_r($result);
echo "\n";
