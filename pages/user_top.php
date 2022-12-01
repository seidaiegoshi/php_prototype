<?php
// var_dump($_GET["project_id"]);

// 各種項目設定
$database_name = "php_ploto";
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
$sql = "SELECT * FROM projects";

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
	$project_abstract_html_element .= "
  <tr>
	<td>{$record["company_id"]}</td>
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

<body>
	<a href="">プロフィール</a>
	<a href="./creator_top.php?project_id=21">商品を作る</a>

	<p>開発中の商品</p>
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

	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>