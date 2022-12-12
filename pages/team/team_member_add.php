<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["team_id"]) || $_POST["team_id"] == "" ||
  !isset($_POST["username"]) || $_POST["username"] == ""
) {
  exit("ParamError");
}


$team_id = $_POST["team_id"];
$username = $_POST["username"];


//DB接続
$pdo = connect_to_db();

// すでにチームメンバーかどうか
$sql = 'SELECT COUNT(*) FROM team_members
LEFT OUTER JOIN(
  SELECT user_id, username FROM users
) AS result
ON team_members.user_id = result.user_id
WHERE username=:username AND team_id=:team_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

if ($stmt->fetchColumn() > 0) {
  echo '<p>すでに登録されているユーザです．</p>';
  echo "<div><a href='./team_member.php?team_id={$team_id}'>メンバー編集ページへ</a></div>";
  exit();
}

// そのユーザー存在してるかどうか
$sql = 'SELECT COUNT(*) FROM users WHERE username=:username';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

if ($stmt->fetchColumn() == 0) {
  echo '<p>' . $username . 'は存在しないユーザーです。</p>';
  echo "<div><a href='./team_member.php?team_id={$team_id}'>メンバー編集ページへ</a></div>";
  exit();
}

// 登録するユーザーID取得
$sql = 'SELECT * FROM users WHERE username=:username';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetch();
$user_id = $result["user_id"];


// SQL作成&実行
$sql = 'INSERT INTO team_members (id, team_id, user_id) VALUES (NULL, :team_id, :user_id);';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

header("Location:./team_member.php?team_id=$team_id");
