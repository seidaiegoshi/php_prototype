<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>create team</title>
  <link rel="stylesheet" type="text/css" href="./../../css/style.css">
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

  <form action="./team_create_act.php" method="POST">
    <fieldset>
      <legend>チームを作る</legend>
      <div>チーム名: <input type="text" name="team_name" /></div>
      <div>チームの説明: <textarea type="text" name="description"></textarea></div>
      <div>
        <button>submit</button>
      </div>
    </fieldset>
  </form>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>