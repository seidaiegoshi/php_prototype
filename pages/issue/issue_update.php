<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();


var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["project_id"]) || $_POST["project_id"] == "" ||
  !isset($_POST["issue_id"]) || $_POST["issue_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == ""
) {
  exit("ParamError");
}


$project_id = $_POST["project_id"];
$issue_id = $_POST["issue_id"];
$title = $_POST["title"];
$content = $_POST["content"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．

// SQL作成&実行
$sql = 'UPDATE issues 
SET title=:title, content=:content, updated_at=now()
WHERE issue_id=:issue_id';

$stmt = $pdo->prepare($sql);


// バインド変数を設定
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':issue_id', $issue_id, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
header("Location:./../project/project_detail.php?project_id=" . $project_id);
