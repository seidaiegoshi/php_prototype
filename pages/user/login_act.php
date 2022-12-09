<?php
// データ受け取り
session_start();
include('./../functions/db.php');


// POSTの値がなかったら、ユーザ有無でエラーが出るのでPOSTの確認はしない


$username = $_POST["username"];
$password = $_POST["password"];


// DB接続
$pdo = connect_to_db();


// SQL実行
$sql = 'SELECT * FROM users 
WHERE username=:username AND password=:password';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':password', $password, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// ユーザ有無で条件分岐
$val = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$val) {
  echo "<p>ログイン情報に誤りがあります。</p>";
  echo "<a href='./login.html'>ログインページへ</a>";
} else {
  $_SESSION = array();
  $_SESSION["session_id"] = session_id();
  $_SESSION["username"] = $val["username"];
  $_SESSION["user_id"] = $val["user_id"];
  header("Location:./../user_top.php");
}
