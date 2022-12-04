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
  <tr>
	<td>{$record["team_id"]}</td>
	<td>{$record["category_id"]}</td>
		<td>{$record["title"]}</td>
    <td>{$record["content"]}</td>
    <td>{$record["deadline"]}</td>
    <td>{$record["like_count"]}</td>
    <td>{$record["updated_at"]}</td>
  </tr>
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
</head>

<header>
	<a href="./../index.html">TOP</a>
	<a href="./creator_top.php?team_id=1">商品を作る</a>
	<a href="">プロフィール</a>
</header>

<body>

	<div>
		<div>
			<p>商品を探す</p>
		</div>
		<div>
			<form action="./user_top.php" method="GET">
				<input type="text" name="search">
				<button>検索</button>
			</form>
		</div>
	</div>
	<div>
		<table>
			<thead>
				<td>会社ID</td>
				<td>カテゴリID</td>
				<td>タイトル</td>
				<td>内容</td>
				<td>期限</td>
				<td>イイネ数</td>
				<td>更新日</td>
			</thead>
			<tbody>
				<?= $project_abstract_html_element ?>
			</tbody>
		</table>
	</div>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>