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
	<a href="./creator_top.php?team_id=<?= $team_id ?>">company top</a>
</header>

<body>
	<form action="./project_create.php" method="POST">
		<fieldset>
			<legend>新商品作成</legend>
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