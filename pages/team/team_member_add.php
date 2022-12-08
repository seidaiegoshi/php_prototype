<?php
session_start();
include('./../functions/db.php');
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["username"]) || $_POST["username"] == ""
) {
  exit("ParamError");
}


$team_id = $_POST["team_id"];
$username = $_POST["username"];


//DB接続
$pdo = connect_to_db();

// SQL作成&実行 チームを作成する。
$sql = 'INSERT INTO team_members (id, team_id, username) VALUES (NULL, :team_id, :username);';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./team_member.php?team_id=$team_id");
