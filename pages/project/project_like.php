<?php
// session_start();
include('./../functions/db.php');
// include('./../functions/is_login.php');


if (
  !isset($_GET['user_id']) || $_GET['user_id'] == '' ||
  !isset($_GET['project_id']) || $_GET['project_id'] == ''
) {
  exit('paramError');
}


$user_id = $_GET['user_id'];
$project_id = $_GET['project_id'];

// データベースに接続
$pdo = connect_to_db();

// すでにlikeしているか確認
$sql = 'SELECT COUNT(*) FROM project_like
WHERE user_id = :user_id AND project_id = :project_id';

// バインド
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);

// sql実行
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
// 参照
$like_count = $stmt->fetchColumn();
// 登録または削除
if ($like_count !== 0) {
  // like登録してたら、
  $sql = 'DELETE FROM project_like WHERE user_id=:user_id AND project_id=:project_id';
} else {
  // like登録してなかったら、
  $sql = 'INSERT INTO project_like(id, user_id, project_id, created_at) VALUES(NULL, :user_id, :project_id, now())';
}


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}


header("Location:./project_detail.php?project_id=$project_id");
exit();
