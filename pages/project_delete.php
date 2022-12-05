<?php
include("./functions/db.php");

var_dump($_GET);
$project_id = $_GET['project_id'];

$pdo = connect_to_db();

$sql = 'DELETE FROM projects 
WHERE project_id=:project_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./creator_top.php");
