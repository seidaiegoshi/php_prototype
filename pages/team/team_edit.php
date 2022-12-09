<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

//  todo ユーザーがチームに登録されているメンバーであるか確認する処理

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
$sql = 'SELECT * FROM teams WHERE team_id = :team_id';

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
$result = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>team edit</title>
  <link rel="stylesheet" type="text/css" href="./../../css/style.css">
</head>

<body>
  <form action="./team_edit_act.php" method="POST">
    <fieldset>
      <legend>チームを編集する</legend>
      <input type="hidden" name="team_id" value="<?= $team_id ?>">
      <div>チーム名: <input type="text" name="team_name" value='<?= $result["team_name"] ?>' /></div>
      <div>チームの説明: <textarea type="text" name="description"><?= $result["description"] ?></textarea></div>
      <div>
        <button>submit</button>
      </div>
    </fieldset>
  </form>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>