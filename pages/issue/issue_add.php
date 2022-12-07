<?php
$project_id  = $_GET["project_id"];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>issue dd</title>
	<!-- <link rel="stylesheet" type="text/css" href="./css/style.css"> -->
</head>


<body>
	進捗を追加する画面
	<form action="./issue_create.php" method="POST">
		<fieldset>
			<legend>進捗を追加する</legend>
			<input type="number" name="project_id" value="<?= $project_id ?>" hidden />
			<div>title: <input type="text" name="title" /></div>
			<div>content: <input type="text" name="content" /></div>
			<div>
				<button>submit</button>
			</div>
		</fieldset>
	</form>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>