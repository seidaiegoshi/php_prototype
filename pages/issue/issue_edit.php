<?php
include("./../functions/db.php");
// var_dump($_POST);

// 値がちゃんとあるかチェック。
if (
  !isset($_POST["issue_id"]) || $_POST["issue_id"] == ""
) {
  exit("ParamError");
}

$issue_id = $_POST["issue_id"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'SELECT * FROM issues WHERE issue_id = :issue_id';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':issue_id', $issue_id, PDO::PARAM_STR);

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
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>issue edit</title>
  <!-- <link rel="stylesheet" type="text/css" href="./css/style.css"> -->
</head>


<body>
  <h1>進捗を編集する</h1>
  <form action="./issue_update.php" method="POST">
    <fieldset>
      <legend>進捗を追加する</legend>
      <input type="hidden" name="issue_id" value="<?= $issue_id ?>" />
      <input type="hidden" name="project_id" value="<?= $result["project_id"] ?>" />
      <div>title: <input type="text" name="title" value="<?= $result["title"] ?>" /></div>
      <div>content: <textarea type="text" name="content"><?= $result["content"] ?></textarea></div>
      <div>
        <button>submit</button>
        <a href="./../project/project_detail.php?project_id=<?= $result["project_id"] ?>">cancel</a>

      </div>
    </fieldset>
  </form>
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>