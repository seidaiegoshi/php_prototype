<?php
include("./functions/db.php");

// var_dump($_GET["project_id"]);
if (
	!isset($_GET["project_id"]) || $_GET["project_id"] == ""
) {
	header("Location:./login.html");
}

$project_id = $_GET["project_id"];


//DB接続
$pdo = connect_to_db();

// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
$sql = "SELECT * FROM projects WHERE project_id=$project_id";

$stmt = $pdo->prepare($sql);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}

$result = $stmt->fetch(PDO::FETCH_ASSOC);

$project_abstract_html_element = "";

$project_abstract_html_element .= "
  <div class='magazine'>
	<a class='project' href='./project_detail.php?project_id={$result["project_id"]}'>
		<div class='image'>";
if ($result["image_url"] !== 0) {
	$project_abstract_html_element .= "	
			<img src='{$result["image_url"]}'>";
}
$project_abstract_html_element .= "
		</div>
		<div class='article'>
			<div class='title'>
				{$result["title"]}
			</div>
			<div class='content'>
				{$result["content"]}
			</div>
			<div class='counter'>
				{$result["like_count"]}
				{$result["updated_at"]}
			</div>
		</div>
		</a>	
		</div>
  ";
$team_id = $result["team_id"];

// isseusテーブルのプロジェクトIDがGETで取得したものと一致するやつを取得。
$sql = "SELECT * FROM issues WHERE project_id=$project_id ORDER BY created_at ASC ";

$stmt = $pdo->prepare($sql);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$issues_html_element = "";

foreach ($result as $key => $record) {
	# code...
	$issues_html_element .= "
  <div class='timeline_content'>
		<div class='time'>
    	{$record["created_at"]}
		</div>
		<div class='content'>
			<div class='title'>{$record["title"]}</div>
    	<div>{$record["content"]}</div>
		</div>
  </div>
  ";
}

// var_dump($output);



?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Document</title>
	<link rel="stylesheet" type="text/css" href="./../css/style.css">
	<link rel="stylesheet" type="text/css" href="./../css/project_detail.css">
</head>
<header>
	<div class="header_top">
		<a href="./user_top.php">
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
		<a href="./creator_top.php">
			<div>
				プロフィール
			</div>
		</a>
	</div>
</header>

<body>
	<section class="abstract">
		<h1>開発中の商品</h1>
		<?= $project_abstract_html_element ?>
	</section>

	<section class="progress">
		<h2>開発の進捗</h2>
		<div class="cheer_area">
			<div class="milestone_area">
				<form action="./issue_add.php" method="GET">
					<input type="text" name="project_id" value="<?= $project_id ?>" hidden>
					<button>進捗を追加する</button>
				</form>

				<div>
					<?= $issues_html_element ?>
				</div>
			</div>
			<div class="comment_area">
				user comment area
			</div>
		</div>
	</section>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>