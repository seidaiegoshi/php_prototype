<?php
include("./functions/db.php");

$project_id = $_POST["project_id"];


//DB接続
$pdo = connect_to_db();

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'SELECT * FROM projects WHERE project_id = :project_id';


$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);

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
  <title>project edit</title>
  <link rel="stylesheet" type="text/css" href="./../css/product_edit.css" />
</head>


<body>
  <form action="./project_update.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <legend>プロジェクトの編集</legend>
      <input type="hidden" name="project_id" value="<?= $project_id ?>">
      <img id="project_image" src="<?= $result["image_url"] ?>" alt="project image">
      <label for="upload_image" id="upload_image_label">写真を変更</label>
      <input type="file" name="image" id="upload_image">
      <button type="button" id="return_image">もとに戻す</button>
      <div>product name:
        <input type="text" name="title" value="<?= $result["title"] ?>" />
      </div>
      <div>content:
        <input type="text" name="content" value="<?= $result["content"] ?>" />
      </div>
      <div>deadline:
        <input type="date" name="deadline" value="<?= $result["deadline"] ?>" />
      </div>
      <div>
        <button>submit</button>
        <a href="./creator_top.php">Cancel</a>
      </div>
    </fieldset>
  </form>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script>
    $("#upload_image").change(function(e) {
      const file = e.target.files[0]; //fileを取得
      const reader = new FileReader(); //ファイルリーダーオブジェクトの準備
      reader.readAsDataURL(file); //アップロードしたファイルを読み込み
      reader.onload = (function(file) {
        //与えたファイルをロードできたら、imgタグのソースを変更する。
        $("#project_image").attr("src", reader.result);
      });
      if (file.type.indexOf("image") < 0) {
        // 画像じゃなかったときのエラー処理
        alert("画像を選択してください。");
      }
    });

    $("#return_image").on("click", () => {
      // もとに戻すで初期化
      $("#project_image").attr("src", "<?= $result["image_url"] ?>");
      $("#upload_image").val("");
    })
  </script>
</body>

</html>