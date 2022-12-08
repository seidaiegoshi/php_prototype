<?php
include('./../functions/db.php');
session_start();
include('./../functions/is_login.php');
check_session_id();

//DB接続
$pdo = connect_to_db();


// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
$sql = "SELECT * FROM projects 
WHERE team_id in(
SELECT team_id FROM team_members WHERE username=:username
	)";

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':username', $_SESSION["username"], PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$project_abstract_html_element = "";

foreach ($result as $key => $record) {
	$project_abstract_html_element .= "
  <div class='magazine'>
	<a class='project' href='./../project/project_detail.php?project_id={$record["project_id"]}'>
		<div class='image'>";
	if ($record["image_url"] !== 0) {
		$project_abstract_html_element .= "	
			<img src='./../..{$record["image_url"]}'>";
	}
	$project_abstract_html_element .= "
		</div>
		<div class='article'>
			<div class='title'>
				{$record["title"]}
			</div>
			<div class='content'>
				{$record["content"]}
			</div>
			<div class='counter'>
				{$record["like_count"]}
				{$record["updated_at"]}
			</div>
		</div>
		</a>	

		<div class='edit'>
			<div>
				<div class='project_edit'>
				<form action='./../project/project_edit.php' method='POST'>
				<input type='hidden' name='project_id' value='{$record["project_id"]}'>
				<button>
				EDIT
				</button>
				</form>
				</div>
				<div class='project_delete'>
				<a href='./../project/project_delete.php?project_id={$record["project_id"]}'>
					delete
					</a>
				</div>
			</div>
		</div>
		</div>
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
	<link rel="stylesheet" type="text/css" href="./../../css/creator_top.css">
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
		<a href="./manage_projects.php">
			<div>
				プロフィール
			</div>
		</a>
	</div>
</header>

<body>
	<div>
		<a href="./../team/team_top.php">チーム</a>
	</div>
	<div>
		<a href="./../user/logout.php">ログアウト</a>
	</div>
	<section class="search">
		<h1>自分が携わっている商品</h1>
		<?= $project_abstract_html_element ?>
	</section>
	<!-- <a href="">プロフィール</a> -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>