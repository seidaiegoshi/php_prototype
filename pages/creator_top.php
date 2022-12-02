<?php
if (
	!isset($_GET["company_id"]) || $_GET["company_id"] == ""
) {
	header("Location:./login.html");
}
$company_id = $_GET["company_id"];


// 各種項目設定
$database_name = "php_prototype";
$dbn = "mysql:dbname={$database_name};charset=utf8mb4;port=3306;host=localhost";
$user = 'root';
$pwd = '';

// DB接続
try {
	$pdo = new PDO($dbn, $user, $pwd);
	// exit("ok");
} catch (PDOException $e) {
	echo json_encode(["db error" => "{$e->getMessage()}"]);
	exit();
}

// SQL作成&実行
// プロジェクトテーブルのプロジェクトIDがGETで取得したIDと一致するレコードを取得
$sql = "SELECT * FROM projects WHERE company_id=$company_id";

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

$project_abstract_html_element = "";

foreach ($result as $key => $record) {
	# code...
	// $project_abstract_html_element .= "
	// <tr>
	// <td>{$record["project_id"]}</td>
	// <td>{$record["category_id"]}</td>
	// 	<td>
	// 		<a href='./project_detail.php?project_id={$record["project_id"]}'>
	// 			{$record["title"]}
	// 		</a>
	// 	</td>
	//   <td>{$record["content"]}</td>
	//   <td>{$record["deadline"]}</td>
	//   <td>{$record["like_count"]}</td>
	//   <td>{$record["created_at"]}</td>
	//   <td>{$record["updated_at"]}</td>
	// </tr>

	// ";
	$project_abstract_html_element .= "
		<a class='card' href='./project_detail.php?project_id={$record["project_id"]}'>
			<div class='title'>{$record["title"]}</div>
			<div class='deadline'>{$record["deadline"]}</div>
			<div class='content'>{$record["content"]}</div>
			<div class='updated_at'>{$record["updated_at"]}</div>
			<div class='like_count'>{$record["like_count"]}</div>
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
	<title>Document</title>
	<link rel="stylesheet" type="text/css" href="./../css/style.css">
</head>

<header>
	<a href="./../index.html">TOP</a>
</header>

<body>
	<a href="./project_add.php?company_id=1">新商品を作る</a>
	<p>開発中の商品</p>
	<!-- <table>
		<thead>
			<td>新商品ID</td>
			<td>カテゴリID</td>
			<td>タイトル</td>
			<td>内容</td>
			<td>期限</td>
			<td>イイネ数</td>
			<td>作成日</td>
			<td>更新日</td>
		</thead>
		<tbody>
			<?= $project_abstract_html_element ?>
		</tbody>
	</table> -->
	<section class="cards">
		<?= $project_abstract_html_element ?>
	</section>
	<!-- <a href="">プロフィール</a> -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>