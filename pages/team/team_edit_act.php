<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["team_name"]) || $_POST["team_name"] == "" ||
  !isset($_POST["description"]) || $_POST["description"] == ""
) {
  exit("ParamError");
}


$team_id = $_POST["team_id"];
$team_name = $_POST["team_name"];
$description = $_POST["description"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．

// SQL作成&実行
$sql = 'UPDATE teams 
SET team_name=:team_name, description=:description
WHERE team_id=:team_id';

$stmt = $pdo->prepare($sql);


// バインド変数を設定
$stmt->bindValue(':team_name', $team_name, PDO::PARAM_STR);
$stmt->bindValue(':description', $description, PDO::PARAM_STR);
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
header("Location:./team_top.php");
