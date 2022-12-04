<?php
include("./functions/db.php");

// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["category_id"]) || $_POST["category_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == "" ||
  !isset($_POST["deadline"]) || $_POST["deadline"] == ""
) {
  exit("ParamError");
}

$team_id = $_POST["team_id"];
$category_id = $_POST["category_id"];
$title = $_POST["title"];
$content = $_POST["content"];
$deadline = $_POST["deadline"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'INSERT INTO projects (project_id, team_id, category_id, title,content,created_at,updated_at,deadline,like_count) VALUES (NULL, :team_id, :category_id, :title, :content, now(), now(), :deadline, :like_count );';


$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
$stmt->bindValue(':category_id', 0, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
$stmt->bindValue(':like_count', 0, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
// var_dump($result);
// var_dump($stmt);
$project_id = $pdo->lastInsertId();
// echo "test";
echo "</pre>";
// exit();



header("Location:./project_detail.php?project_id=" . $project_id);
