<?php

include("./functions/db.php");

//DB接続
$pdo = connect_to_db();

// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
if (
	!isset($_GET["search"]) || $_GET["search"] == ""
) {
	$sql = "SELECT * FROM projects";

	$stmt = $pdo->prepare($sql);
} else {
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
	# code...
	$project_abstract_html_element .= "
	<a href='./project_detail.php?project_id={$record["project_id"]}'>
  <div class='magazine'>
		<div class='image'>
			<img src='{$record["image_url"]}'>
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
		</div>
		</a>	
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
	<link rel="stylesheet" type="text/css" href="./../css/user_top.css">
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
	<section class="search">
		<h1>試作中のプロダクトを探す</h1>
		<?= $project_abstract_html_element ?>
	</section>
	<section class="favorite">

	</section>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>