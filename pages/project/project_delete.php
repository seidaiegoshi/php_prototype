<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_GET);

if (
  !isset($_GET["project_id"]) || $_GET["project_id"] == ""
) {
  exit("ParamError");
}

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

header("Location:./../profile/manage_projects.php");
