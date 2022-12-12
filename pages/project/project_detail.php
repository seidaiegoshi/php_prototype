<?php
session_start();
include('./../functions/db.php');
include('./../functions/is_login.php');

// var_dump($_GET["project_id"]);
if (
	!isset($_GET["project_id"]) || $_GET["project_id"] == ""
) {
	header("Location:./../user/login.html");
}

$project_id = $_GET["project_id"];

//ログインしてるかどうか
$is_login = is_login();
if ($is_login) {
	$user_id = $_SESSION["user_id"];
	$header_profile = "
 		<a href='./../profile/manage_projects.php'>
			<div>
				プロフィール
			</div>
		</a>
 ";
} else {
	$header_profile = "
 		<a href='./../user/login.html'>
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
$sql = "SELECT * FROM projects 
LEFT OUTER JOIN(
	SELECT project_id AS like_project, COUNT(id) AS like_count
	FROM project_like
	GROUP BY project_id
) AS result_table
ON projects.project_id = result_table.like_project
WHERE project_id=:project_id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}

$result = $stmt->fetch(PDO::FETCH_ASSOC);

$team_id = $result["team_id"];


$project_abstract_html_element = "";

$project_abstract_html_element .= "
  <div class='magazine'>
		<div class='image'>";
if ($result["image_url"] !== NULL) {
	$project_abstract_html_element .= "	
			<img src='./../..{$result["image_url"]}'>";
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
				{$result["title"]}
			</div>
			<div class='content'>
				{$result["content"]}
			</div>
			<div class='like_button'>
				<div class='counter'>
					<span class='like_icon'>";
if ($is_login == true) {
	$project_abstract_html_element .= "
					<a href='./project_like.php?project_id={$result["project_id"]}&user_id={$user_id}'>";
} else {
	$project_abstract_html_element .= "
					<a href=''>";
}
$project_abstract_html_element .= "

						<i class='fa-solid fa-heart'></i>
					</a>	
					</span>
					<span>{$result["like_count"]}</span>
				</div>
			</div>
			<div class='updated_at'>
				{$result["updated_at"]}
			</div>
		</div>
	</div>
  ";


if ($is_login == true) {
	// チームに自分が含まれているか確認する。
	$sql = "SELECT COUNT(*) FROM team_members 
WHERE team_id=:team_id AND user_id=:user_id";

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':team_id', $team_id, PDO::PARAM_STR);
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);


	try {
		$status = $stmt->execute();
		// var_dump($status);
	} catch (PDOException $e) {
		echo json_encode(["sql error" => "{$e->getMessage()}"]);
		exit();
	}
	if ($stmt->fetchColumn() !== 0) {
		// チームメンバーの場合
		$is_member = true;
	} else {
		// チームメンバーじゃない場合
		$is_member = false;
	}
} else {
	$is_member = false;
}

// issuesテーブルのプロジェクトIDがGETで取得したものと一致するやつを取得。
$sql = "SELECT * FROM issues WHERE project_id=:project_id ORDER BY created_at DESC ";


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);


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

	if ($is_member) {
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
if ($is_member) {
	$add_progress = "
	<form action='./../issue/issue_add.php' method='GET'>
		<input type='hidden' name='project_id' value='{$project_id}' >
		<button>進捗を追加する</button>
	</form>
	";
}

// 表示しているプロジェクトのチームメンバー一覧を取得
$sql = "SELECT * FROM team_members
WHERE team_id IN(
    SELECT team_id
    FROM projects
    WHERE project_id=:project_id
    )
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// メンバーを保存する配列
$members = array();
foreach ($team_members as $key => $value) {
	$members[] = $value["user_id"]; //配列にメンバーのidを追加
}

// コメント取得SQL
$sql = "SELECT * FROM project_comment 
LEFT OUTER JOIN(
	SELECT user_id,username FROM users
) AS result
ON project_comment.user_id = result.user_id
WHERE project_id=:project_id ORDER BY created_at DESC ";


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':project_id', $project_id, PDO::PARAM_STR);
// $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
	$status = $stmt->execute();
	// var_dump($status);
} catch (PDOException $e) {
	echo json_encode(["sql error" => "{$e->getMessage()}"]);
	exit();
}


// ログインしたたらコメントできる。
if ($is_login) {
	$html_comment = "
<form action='./project_comment.php' method='POST' class='chat_form'>
	<input type='text' name='comment'>
	<input type='hidden' name='project_id' value='{$project_id}'>
	<input type='hidden' name='user_id' value='{$user_id}'>
	<button>SEND</button>
</form>
";
} else {
	$html_comment = "";
}


$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// コメントを表示
foreach ($result as $key => $value) {
	$html_comment .= "
	<div class='comment_line'>
	";
	if (in_array($value["user_id"], $members)) {
		$html_comment .= "<span class='mark'><i class='fa-solid fa-industry'></i></span>";
	}
	$html_comment .= "
	<span class='username'>{$value["username"]}</span>
	<span class='comment'>{$value["comment"]}</span>
	<span class='datetime'>{$value["created_at"]}</span>
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
	<link rel="stylesheet" type="text/css" href="./../../css/project_detail.css">
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
		<?= $header_profile ?>
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
				<?= $add_progress ?>
				<div>
					<?= $issues_html_element ?>
				</div>
			</div>
			<div class="comment_area">
				<?= $html_comment ?>
			</div>
		</div>
	</section>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
</body>

</html>