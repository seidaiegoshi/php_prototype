<?php
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["company_id"]) || $_POST["company_id"] == "" ||
  !isset($_POST["category_id"]) || $_POST["category_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == "" ||
  !isset($_POST["deadline"]) || $_POST["deadline"] == ""
) {
  exit("ParamError");
}

$company_id = $_POST["company_id"];
$category_id = $_POST["category_id"];
$title = $_POST["title"];
$content = $_POST["content"];
$deadline = $_POST["deadline"];

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
$sql = 'INSERT INTO projects (project_id, company_id, category_id, title,content,created_at,updated_at,deadline,like_count) VALUES (NULL, :company_id, :category_id, :title, :content, now(), now(), :deadline, :like_count );
SELECT AUTO_INCREMENT FROM information_schema.tables
WHERE table_schema = php_ploto and table_name = projects;';


$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':company_id', $company_id, PDO::PARAM_STR);
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
