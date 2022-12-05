<?php
if (
	!isset($_GET["team_id"]) || $_GET["team_id"] == ""
) {
	header("Location:./login.html");
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
	<link rel="stylesheet" type="text/css" href="./../css/style.css" />
</head>
<header>
	<div class="header_top">
		<a href="./../index.html">
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
		<a href="">
			<div>
				プロフィール
			</div>
		</a>
	</div>
</header>

<body>
	<form action="./project_create.php" method="POST" enctype="multipart/form-data">
		<fieldset>
			<legend>新商品作成</legend>
			<input type="file" name="image">
			<input type="number" name="team_id" value="<?= $team_id ?>" hidden />
			<div>product name: <input type="text" name="title" /></div>
			<div>content: <input type="text" name="content" /></div>
			<div>deadline: <input type="date" name="deadline" /></div>
			<div>
				<button>submit</button>
			</div>
		</fieldset>
	</form>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>