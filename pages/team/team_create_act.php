<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_name"]) || $_POST["team_name"] == "" ||
  !isset($_POST["description"]) || $_POST["description"] == ""
) {
  exit("ParamError");
}


$team_name = $_POST["team_name"];
$description = $_POST["description"];


//DB接続
$pdo = connect_to_db();

// SQL作成&実行 チームを作成する。
$sql = 'INSERT INTO teams (team_id, team_name, description) VALUES (NULL, :team_name, :description);';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_name', $team_name, PDO::PARAM_STR);
$stmt->bindValue(':description', $description, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// 今作ったチームIDを取得する。
$team_id =  $pdo->lastInsertId();


// チームメンバーとして自分を追加する。
// 新規作成のチームなので、すでにチームメンバーか確認する必要はない。
// SQL作成&実行
$sql = 'INSERT INTO team_members (id, team_id, username) VALUES (NULL, :team_id, :username);';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
$stmt->bindValue(':username', $_SESSION["username"], PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./team_top.php?team_id=$team_id");
