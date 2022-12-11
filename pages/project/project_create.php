<?php
include("./../functions/db.php");
session_start();
include('./../functions/is_login.php');
check_session_id();

//画像の処理
if (!isset($_POST["image"]) || $_POST["image"] == "") {


  if (!empty($_FILES)) {
    // $_FILES['image']['name']もとのファイルの名前
    // $_FILES['image']['tmp_name']サーバーにある一時ファイルの名前
    $filename = uniqid() . $_FILES['image']['name'];
    $uploaded_path = './../../data/images/' . $filename;

    $result = move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_path);

    if ($result) {
      $MSG = 'アップロード成功！';
      $image_url = '/data/images/' . $filename;
    } else {
      $MSG = 'アップロード失敗！エラーコード：' . $_FILES['image']['error'];
    }
  } else {
    $MSG = '画像を選択してください';
  }
  echo $MSG;
}
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["title"]) || $_POST["title"] == "" ||
  !isset($_POST["content"]) || $_POST["content"] == "" ||
  !isset($_POST["deadline"]) || $_POST["deadline"] == ""
) {
  exit("ParamError");
}

$team_id = $_POST["team_id"];
$title = $_POST["title"];
$content = $_POST["content"];
$deadline = $_POST["deadline"];


//DB接続
$pdo = connect_to_db();

// SQL作成&実行
if (!empty($image_url)) {
  // 画像あるなら
  $sql = 'INSERT INTO projects (project_id, team_id, title, image_url, content,created_at,updated_at,deadline) VALUES (NULL, :team_id,  :title, :image_url, :content, now(), now(), :deadline  );';
  $stmt = $pdo->prepare($sql);

  // バインド変数を設定
  $stmt->bindValue(':image_url', $image_url, PDO::PARAM_STR);
} else {
  // 画像ないなら
  $sql = 'INSERT INTO projects (project_id, team_id, title, image_url, content,created_at,updated_at,deadline) VALUES (NULL, :team_id,  :title, NULL, :content, now(), now(), :deadline );';
  $stmt = $pdo->prepare($sql);
}




// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':content', $content, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);

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
