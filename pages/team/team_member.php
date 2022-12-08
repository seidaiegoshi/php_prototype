<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

// 値がちゃんとあるかチェック。
if (
  !isset($_GET["team_id"]) || $_GET["team_id"] == ""
) {
  exit("ParamError");
}

$team_id = $_GET["team_id"];

//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'SELECT * FROM team_members WHERE team_id = :team_id';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html_element = "";

foreach ($result as $key => $value) {
  $html_element .= "
  <li>
    <div>
      <div class='name'>{$value["username"]}</div>
      <a href=',.team_member_delete.php'>delete</a>
      </div>
  </li>
  
  ";
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>team member</title>
  <link rel="stylesheet" type="text/css" href="./../../css/team_member.css">
</head>

<body>
  <div>
    <form action="./team_member_add.php" method="POST">
      <p>add member</p>
      <input type="hidden" name="team_id" value="<?= $team_id ?>">
      <input type="text" name="username" placeholder="username">
      <button>add</button>
    </form>
    <div>
      <p>current user</p>
      <ul>
        <?= $html_element ?>
      </ul>
    </div>
  </div>
  <a href="./team_top.php">チーム管理画面へ</a>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>