<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "utf-8">
</head>
<body>




<?php

//3-1.MySQLへ接続。
$dsn = 'mysql:dbname=;host=localhost';//ホスト名は.と-は＿に変える
$user = '.';//ユーザー名は13文字
$password = '';
$pdo = new PDO($dsn,$user,$password);//MySQLへ接続

//3-2.テーブル作成
$sql= "CREATE TABLE tbtest"//CREATE　TABLE　テーブル名
 ." ("
 . "id INT AUTO_INCREMENT,"//フィールド名　データ型（長さ）
 . "name char(32),"
 . "comment TEXT,"
 . "password char(32),"
 . "time TIMESTAMP,"
 . "INDEX(id)"
 .");";
 $stmt = $pdo->query($sql);//SQL文の実行




//新規投稿（コメントが送信されたとき、空白でないとき、編集番号が空白のとき）
if (isset($_POST["name"])&&($_POST["name"])!=""&&isset($_POST["comment"])&&($_POST["comment"])!= ""){
	if($_POST["edit2"]==""){
		$sql = 'SELECT NOW();';
		$time = $pdo->query($sql); 

	//3-5.データを入力
	$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment,password) VALUES (:name, :comment,:password)");
	//INSERT　INTO　テーブル名（カラム名）VALUES（登録データ）
	//カラム名、登録データはカンマ,で区切って指定。登録データは''で囲む
		
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);//（'登録データ',' 変数','型'）型について、PDO::PARAM_STRは、文字列という意味
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$password = $_POST["password"];
	$sql -> execute();//executeで命令を実行させる
	}
}





if(isset($_POST["edit"])&&$_POST["edit"]!=""){
	$editid = $_POST["edit"];
	$editpassword = $_POST["editpassword"];
	//既存の名前、コメント、パスワードを取得
	$sql = "SELECT * FROM tbtest";//編集対象番号に書かれているIDを指定
	$stmt = $pdo->query($sql);
	foreach ($stmt as $row){
		if($editid == $row['id']){
			if($editpassword == $row['password']){
				$beforeeditname = $row['name'];
				$beforeeditcomment = $row['comment'];
			}
		}
	}
}




if(isset($_POST["delete"])&&($_POST["delete"])!= ""){//削除欄に書き込みがあったら
	$deleteid = $_POST["delete"];
	$deletepassword = $_POST["deletepassword"];
	//既存の名前、コメント、パスワードを取得
	$sql = "SELECT * FROM tbtest ";
	$stmt = $pdo->query($sql);
	foreach ($stmt as $row){
		if($deleteid==$row['id']){
			if($deletepassword == $row['password']){//正規パスワードと削除パスワードが一緒だったら
				//3-8.入力データを削除する
				$sql = "delete from tbtest where id=$deleteid";
				//DELETE　FROM　テーブル名　WHERE　削除するレコードのid
				$result = $pdo->query($sql);
			}
		}
	}
}



?>


<form action = "mission_4-1.php" method = "post">
<input type = "text" name = "name" value = 
<?php
if(isset($_POST["edit"])){//名前のフォーム作成
	echo $beforeeditname;
	}else{
	echo "名前";
	}
?>
><br/>
<input type = "text" name ="comment" value = 
<?php
if(isset($_POST["edit"])){//コメントのフォーム作成
	echo $beforeeditcomment;
	}else{
	echo "コメント";
	}
?>
><br/>
<input type = "hidden" name = "edit2" value = 
<?php
if(isset($_POST["edit"])){//編集番号を隠す
	echo $_POST["edit"];
	}
?>
><br/>
<input type = "text" name = "password" value = "パスワード"><br/>
<input type = "submit" name = "sub" value = "送信"><br/>
</form>
<form action = "mission_4-1.php" method = "post">
<input type = "text" name = "delete" value = "削除対象番号"><br/>
<input type = "text" name = "deletepassword" value = "パスワード"><br/>
<input type = "submit" name = "delete1" value = "削除">
</form>

<form action = "mission_4-1.php" method = "post">
<input type = "text" name = "edit" value = "編集対象番号"><br/>
<input type = "text" name = "editpassword" value = "パスワード"><br/>
<input type = "submit" name = "sub2" value = "編集"><br/>
</form>

<?php

if (isset($_POST["name"])&&($_POST["name"])!=""&&isset($_POST["comment"])&&($_POST["comment"])!= ""){
	if(isset($_POST["edit2"])){//隠された編集対象番号が送信されたら
		$id = $_POST["edit2"];
		$nm = $_POST["name"];
		$kome = $_POST["comment"];
		$pass = $_POST["password"];
		
		$sql = "SELECT * FROM tbtest";
		$result = $pdo->query($sql);

		$sql = "update tbtest set name='$nm' , comment='$kome' , password='$pass' where id = $id";
		//UPDATE　テーブル名　SET　カラム名＝登録したいデータ　WHERE　更新データを特定するための条件式
		$result = $pdo->query($sql);
	}
}


//3-6.入力したデータを表示させる
$sql = 'SELECT * FROM tbtest ORDER BY id';//sql文を作成　'SELECT * FROM テーブル名'
$results = $pdo -> query($sql);//データを取得
foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['time'].'<br>';
	}
?>

</body>
</html>