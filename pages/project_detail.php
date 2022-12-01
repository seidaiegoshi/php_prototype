<?php
var_dump($_GET["project_id"]);
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

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．


// SQL作成&実行
$sql = 'SELECT * FROM projects WHERE project_id=}';

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

foreach ($result as $key => $record) {
	# code...
	$output .= "
  <tr>
    <td>{$record["deadline"]}</td>
    <td>{$record["todo"]}</td>
  </tr>
  ";
}

// var_dump($result);



?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Document</title>
	<!-- <link rel="stylesheet" type="text/css" href="./css/style.css"> -->
</head>

<body>
	プロジェクトの詳細
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>