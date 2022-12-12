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
LEFT OUTER JOIN(
SELECT project_id AS like_project_id,COUNT(id) AS like_count
FROM project_like
GROUP BY project_id
) AS result
ON projects.project_id = result.like_project_id
WHERE team_id in(
SELECT team_id FROM team_members WHERE user_id=:user_id
)
";

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);

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
	if ($record["image_url"] !== NULL) {
		$project_abstract_html_element .= "	
			<img src='./../..{$record["image_url"]}'>";
	} else {
		$project_abstract_html_element .= "	
			<div class='no_image'>
			<p>no image</p>	
			</div>
		";
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

			<div class='updated_at'>
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


// お気に入りに登録しているプロジェクトを表示
// SQL作成&実行
$sql = "SELECT * FROM projects
INNER JOIN(
SELECT project_id AS like_project_id,user_id 
FROM project_like
   WHERE user_id=:user_id
) AS result
ON projects.project_id = result.like_project_id
";

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$favorite_projects = "";

foreach ($result as $key => $record) {
	$favorite_projects .= "
  <div class='magazine'>
	<a class='project' href='./../project/project_detail.php?project_id={$record["project_id"]}'>
		<div class='image'>";
	if ($record["image_url"] !== NULL) {
		$favorite_projects .= "	
			<img src='./../..{$record["image_url"]}'>";
	} else {
		$favorite_projects .= "	
			<div class='no_image'>
			<p>no image</p>	
			</div>
		";
	}
	$favorite_projects .= "
		</div>
		<div class='article'>
			<div class='title'>
				{$record["title"]}
			</div>
			<div class='content'>
				{$record["content"]}
			</div>

			<div class='updated_at'>
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
	<link rel="stylesheet" type="text/css" href="./../../css/like_button.css">
	<script src="https://kit.fontawesome.com/66d795ff86.js" crossorigin="anonymous"></script>
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
				<span>こんにちは<?= $_SESSION["username"] ?>さん</span>
				プロフィール
			</div>
		</a>
	</div>
</header>

<body>
	<div class="menu">
		<div>
			<a href="./../team/team_top.php">チーム</a>
		</div>
		<div>
			<a href="./../user/logout.php">ログアウト</a>
		</div>
	</div>
	<section class="search">
		<h1>自分が携わっている商品</h1>
		<?= $project_abstract_html_element ?>
	</section>
	<section class="favorite">
		<h1>お気に入りのプロジェクト</h1>
		<?= $favorite_projects ?>
	</section>
	<!-- <a href="">プロフィール</a> -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>