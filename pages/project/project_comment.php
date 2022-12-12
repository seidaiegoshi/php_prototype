<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();
var_dump($_POST);
// 値がちゃんとあるかチェック。
if (
  !isset($_POST["project_id"]) || $_POST["project_id"] == "" ||
  !isset($_POST["user_id"]) || $_POST["user_id"] == "" ||
  !isset($_POST["comment"]) || $_POST["comment"] == ""
) {
  exit("ParamError");
}

$project_id = $_POST["project_id"];
$user_id = $_POST["user_id"];
$comment = $_POST["comment"];


//DB接続
$pdo = connect_to_db();

// SQL作成&実行
// 画像あるなら
$sql = 'INSERT INTO project_comment (id, project_id, user_id, comment, created_at) VALUES (NULL, :project_id, :user_id, :comment, now());';
$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Location:./project_detail.php?project_id=" . $project_id);
