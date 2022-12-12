<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);

if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["user_id"]) || $_POST["user_id"] == ""
) {
  exit("ParamError");
}

$user_id = $_POST['user_id'];
$team_id = $_POST['team_id'];

$pdo = connect_to_db();

$sql = 'DELETE FROM team_members 
WHERE user_id=:user_id AND team_id=:team_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./team_member.php?team_id=" . $team_id);
