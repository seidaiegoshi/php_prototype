<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();


//DB接続
$pdo = connect_to_db();


// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
$sql = "SELECT * FROM teams 
WHERE team_id in (
SELECT team_id FROM team_members WHERE username=:username
	)";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':username', $_SESSION["username"], PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
  $status = $stmt->execute();
  // var_dump($status);
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


$html_team_element = "";
foreach ($result as $key => $value) {
  $html_team_element .= "
  <div class='team_content'>
    <div class='abstract'>
      <div class='team_name'>
        {$value["team_name"]}
      </div>
      <div class='team_description'>
        {$value["description"]}
      </div>
      <div>
        <a href='./../project/project_add.php?team_id={$value["team_id"]}'>新商品を作る</a>
      </div>
      <div>
      <a href='./team_edit.php?team_id={$value["team_id"]}'>チームの編集</a>
      </div>
      <div>
        <a href='./team_member.php?team_id={$value["team_id"]}'>チームメンバーを見る</a>
      </div>
      <div>
        <a href='./team_delete.php'>チームを削除</a>
      </div>
    </div>
    <div class='products'>
    
    </div>
  </div>
";
}




?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>team top</title>
  <link rel="stylesheet" type="text/css" href="./../../css/style.css">
  <link rel="stylesheet" type="text/css" href="./../../css/team_top.css">
</head>

<header>
  <div class="header_top">
    <a href="./../user_top.php">
      <div>
        TOP
      </div>
    </a>
  </div>
  <div class="header_search">
    <form action="./user_top.php" method="GET">
      <input type="text" name="search">
      <button>検索</button>
    </form>
  </div>
  <div class="header_profile">
    <a href="./../profile/manage_projects.php">
      <div>
        プロフィール
      </div>
    </a>
  </div>
</header>

<body>
  <div>
    <a href="./team_create.php">チームを作る</a>
  </div>
  <h1>自分が参加しているチーム</h1>
  <div class="team_list">
    <?= $html_team_element ?>
  </div>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>