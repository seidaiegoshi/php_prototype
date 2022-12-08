<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);

if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["username"]) || $_POST["username"] == ""
) {
  exit("ParamError");
}

$username = $_POST['username'];
$team_id = $_POST['team_id'];

$pdo = connect_to_db();

$sql = 'DELETE FROM team_members 
WHERE username=:username AND team_id=:team_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./team_member.php?team_id=" . $team_id);
