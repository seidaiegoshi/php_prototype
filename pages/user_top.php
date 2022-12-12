<?php

include("./functions/db.php");
session_start();
include("./functions/is_login.php");

// ログイン判定
$is_login = is_login();
if ($is_login) {
	$header_profile = "
 		<a href='./profile/manage_projects.php'>
			<div>
				プロフィール
			</div>
		</a>
 ";
} else {
	$header_profile = "
 		<a href='./user/login.html'>
			<div>
				ログイン
			</div>
		</a>
 ";
}

//DB接続
$pdo = connect_to_db();

// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
if (
	!isset($_GET["search"]) || $_GET["search"] == ""
) {
	$sql = "SELECT * FROM projects LEFT OUTER JOIN
	(
		SELECT project_id AS like_project_id, COUNT(id) AS like_count
		FROM project_like
		GROUP BY project_id
	) AS  like_result
	ON projects.project_id = like_result.like_project_id";

	$stmt = $pdo->prepare($sql);
} else {
	// 検索でやってきた場合
	$search_word = $_GET["search"];
	$sql = "SELECT * FROM projects 
	WHERE title LIKE :search_word OR content LIKE :search_word";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':search_word', "%" . $search_word . "%", PDO::PARAM_STR);
}


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
		<a class='project' href='./project/project_detail.php?project_id={$record["project_id"]}'>
				<div class='image'>";
	if ($record["image_url"] !== NULL) {
		$project_abstract_html_element .= "	
			<img src='./..{$record["image_url"]}'>";
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
					<div class='like_button'>
						<div class='counter'>
							<span class='like_icon'><i class='fa-solid fa-heart'></i></span><span>{$record["like_count"]}</span>
						</div>
					</div>
					<div class='updated_at'>
						{$record["updated_at"]}
					</div>
				</div>
			</a>	
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
	<title>user top</title>
	<link rel="stylesheet" type="text/css" href="./../css/style.css">
	<link rel="stylesheet" type="text/css" href="./../css/like_button.css">
	<script src="https://kit.fontawesome.com/66d795ff86.js" crossorigin="anonymous"></script>
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
		<?= $header_profile ?>
	</div>
</header>

<body>
	<section class="search">
		<h1>試作中の商品を見つける</h1>
		<?= $project_abstract_html_element ?>
	</section>
	<section class="favorite">

	</section>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>