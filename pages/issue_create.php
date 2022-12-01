<?php
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

// DB接続

// 各種項目設定
$database_name = "php_ploto";
$dbn = "mysql:dbname={$database_name};charset=utf8mb4;port=3306;host=localhost";
$user = 'root';
$pwd = '';

// DB接続
try {
  $pdo = new PDO($dbn, $user, $pwd);
  // exit("ok");
} catch (PDOException $e) {
  echo json_encode(["db error" => "{$e->getMessage()}"]);
  exit();
}

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'INSERT INTO issues (issue_id, project_id,  title, content, created_at, updated_at, like_count) VALUES (NULL, :project_id,  :title, :content, now(), now(), :like_count );';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':like_count', 0, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


header("Location:./project_detail.php?project_id=" . $project_id);
