<?php
if (
	!isset($_GET["team_id"]) || $_GET["team_id"] == ""
) {
	header("Location:./../user/login.html");
}
$team_id = $_GET["team_id"];

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Document</title>
	<link rel="stylesheet" type="text/css" href="./../../css/style.css" />
	<link rel="stylesheet" type="text/css" href="./../../css/product_edit.css" />
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
		<a href="./manage_projects.php">
			<div>
				プロフィール
			</div>
		</a>
	</div>
</header>

<body>
	<form action="./../project/project_create.php" method="POST" enctype="multipart/form-data">
		<fieldset>
			<legend>新商品作成</legend>
			<img id="project_image" src="" alt="project image">
			<label for="upload_image" id="upload_image_label">写真を変更</label>
			<input type="file" name="image" id="upload_image">
			<input type="hidden" name="team_id" value="<?= $team_id ?>" />
			<div>product name: <input type="text" name="title" /></div>
			<div>content: <textarea type="text" name="content"></textarea></div>
			<div>deadline: <input type="date" name="deadline" /></div>
			<div>
				<button>submit</button>
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
	</script>
</body>

</html>