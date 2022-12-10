<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();

var_dump($_POST);
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["project_id"]) || $_POST["project_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == ""
) {
  exit("ParamError");
}

$project_id = $_POST["project_id"];
$title = $_POST["title"];
$content = $_POST["content"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'INSERT INTO issues (id, project_id,  title, content, created_at, updated_at) VALUES (NULL, :project_id,  :title, :content, now(), now());';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


header("Location:./../project/project_detail.php?project_id=" . $project_id);
