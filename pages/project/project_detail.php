<?php
session_start();
include('./../functions/db.php');
include('./../functions/is_login.php');
check_session_id();

// var_dump($_GET["project_id"]);
if (
	!isset($_GET["project_id"]) || $_GET["project_id"] == ""
) {
	header("Location:./../user/login.html");
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
$user_id = $result["user_id"];

$project_abstract_html_element = "";

$project_abstract_html_element .= "
  <div class='magazine'>
		<div class='image'>";
if ($result["image_url"] !== 0) {
	$project_abstract_html_element .= "	
			<img src='./../../{$result["image_url"]}'>";
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
	</div>
  ";
$team_id = $result["team_id"];

// isseusテーブルのプロジェクトIDがGETで取得したものと一致するやつを取得。
$sql = "SELECT * FROM issues WHERE project_id=$project_id ORDER BY created_at DESC ";

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
		</div>";

	if ($user_id == $_SESSION["user_id"]) {
		$issues_html_element .= "
		<div class='edit'>
			<div>	
				<form action='./../issue/issue_edit.php' method='POST'>
					<input type='hidden' name='issue_id' value='{$record["issue_id"]}'>
					<button>EDIT</button>
				</form>
			</div>
			<div class='delete'>
				<a href='./../issue/issue_delete.php?project_id={$project_id}&issue_id={$record["issue_id"]}'>delete</a>
			</div>
		</div>";
	}
	$issues_html_element .= "	
  </div>
  ";
}


// 進捗を追加するボタン
$add_progress = "";
if ($user_id == $_SESSION["user_id"]) {
	$add_progress = "
	<form action='./../issue/issue_add.php' method='GET'>
		<input type='hidden' name='project_id' value='{$project_id}' >
		<button>進捗を追加する</button>
	</form>
	";
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Document</title>
	<link rel="stylesheet" type="text/css" href="./../../css/style.css">
	<link rel="stylesheet" type="text/css" href="./../../css/project_detail.css">
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
		<form action="./../user_top.php" method="GET">
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
	<section class="abstract">
		<h1>開発中の商品</h1>
		<?= $project_abstract_html_element ?>
	</section>

	<section class="progress">
		<h2>開発の進捗</h2>
		<div class="cheer_area">
			<div class="milestone_area">
				<div>
					<?= $issues_html_element ?>
				</div>
				<?= $add_progress ?>
			</div>
			<div class="comment_area">
				user comment area
			</div>
		</div>
	</section>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>