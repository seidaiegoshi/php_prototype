<?php
include("./functions/db.php");


//画像の処理
if (!isset($_POST["image"]) || $_POST["image"] == "") {
  if (!empty($_FILES)) {
    // $_FILES['image']['name']もとのファイルの名前
    // $_FILES['image']['tmp_name']サーバーにある一時ファイルの名前
    $filename = uniqid() . $_FILES['image']['name'];
    $uploaded_path = './../data/images/' . $filename;

    $result = move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_path);

    if ($result) {
      $MSG = 'アップロード成功！';
      $img_path = $uploaded_path;
    } else {
      $MSG = 'アップロード失敗！エラーコード：' . $_FILES['image']['error'];
    }
  } else {
    $MSG = '画像を選択してください';
  }
  echo $MSG;

  $image_url = $img_path;
}

// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["old_image_url"]) || $_POST["old_image_url"] == "" ||
  !isset($_POST["project_id"]) || $_POST["project_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == "" ||
  !isset($_POST["deadline"]) || $_POST["deadline"] == ""
) {
  exit("ParamError");
}


$project_id = $_POST["project_id"];
$title = $_POST["title"];
$content = $_POST["content"];
$deadline = $_POST["deadline"];
$old_image_url = $_POST["old_image_url"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．

// SQL作成&実行
if (!empty($image_url)) {
  $sql = 'UPDATE projects 
SET title=:title, image_url=:image_url, content=:content, updated_at=now() ,deadline = :deadline
WHERE project_id=:project_id';

  $stmt = $pdo->prepare($sql);

  $stmt->bindValue(':image_url', $img_path, PDO::PARAM_STR);
} else {
  $sql = 'UPDATE projects 
SET title=:title, content=:content, updated_at=now() ,deadline = :deadline
WHERE project_id=:project_id';

  $stmt = $pdo->prepare($sql);
}



// バインド変数を設定
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
if (!empty($image_url)) {
  if (file_exists($old_image_url)) {
    unlink($old_image_url);
  }
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
// var_dump($result);
// var_dump($stmt);
$project_id = $pdo->lastInsertId();
// echo "test";
echo "</pre>";
// exit();



header("Location:./creator_top.php");
