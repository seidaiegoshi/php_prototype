<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_GET);

// 値がちゃんとあるかチェック。
if (
  !isset($_GET["project_id"]) || $_GET["project_id"] == "" ||
  !isset($_GET["issue_id"]) || $_GET["issue_id"] == ""
) {
  exit("ParamError");
}

$project_id = $_GET['project_id'];
$issue_id = $_GET['issue_id'];

$pdo = connect_to_db();

$sql = 'DELETE FROM issues 
WHERE issue_id=:issue_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':issue_id', $issue_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./../project/project_detail.php?project_id=" . $project_id);
