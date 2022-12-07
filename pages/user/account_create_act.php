<?php
include('./../functions/db.php');

if (
  !isset($_POST['username']) || $_POST['username'] == '' ||
  !isset($_POST['password']) || $_POST['password'] == ''
) {
  exit('paramError');
}

$username = $_POST["username"];
$password = $_POST["password"];

$pdo = connect_to_db();

// 同じ名前のユーザーがいるかチェック
$sql = 'SELECT COUNT(*) FROM users WHERE username=:username';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

if ($stmt->fetchColumn() > 0) {
  echo '<p>すでに登録されているユーザです．</p>';
  echo '<div><a href="./login.html">ログインページへ</a></div>';
  echo '<div><a href="./account_create.html">ユーザ登録ページへ</a></div>';
  exit();
}

// 同じ名前のユーザーがいなかったら登録
$sql = 'INSERT INTO users(user_id, username, password) VALUES(NULL, :username, :password)';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// ユーザー情報をセッションに
$val = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION = array();
$_SESSION["session_id"] = session_id();
$_SESSION["username"] = $val["username"];


header("Location:./../user_top.php");
exit();
