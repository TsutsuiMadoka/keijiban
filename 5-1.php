<?php 
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS message_board1"
    ." ("
 	. "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name VARCHAR(32),"
    . "comment TEXT,"
    . "date datetime"
    .");";
    $stmt = $pdo->query($sql);
    
    if(!empty($_POST["name"])&&!empty($_POST["comment"])) {
        $name = $_POST['name'];
	    $comment = $_POST['comment'];
	    $date = date("Y/m/d/H:i:d");
	   
        if(empty($_POST["num_thrown"])) {//新規登録
    
            $sql = $pdo -> prepare("INSERT INTO message_board1 (name, comment, date) VALUES (:name, :comment, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    
            $sql -> execute();
            
        } else {$edit_num = $_POST['edit_num'];//投稿編集
            $editNum = $_POST["num_thrown"];
            
            $sql = "SELECT * FROM message_board1 WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', $editNum, PDO::PARAM_INT);
            $stmt -> execute();
            
            $row = $stmt->fetch();
           
            $sql = 'UPDATE message_board1 SET name=:name,comment=:comment,date=:date WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            
            $stmt -> bindParam(':id', $editNum, PDO::PARAM_INT);
            $stmt -> execute();
           
        }
    }
    
    //削除機能
    if(!empty($_POST['delete'])) {
        $del_num = $_POST['del_num'];
        
        $stmt = $pdo->prepare('delete from message_board1 where id=:id');
        
        //$stmt -> bindParam('i', $del_num, PDO::PARAM_INT);
        $stmt->bindParam(':id', $del_num, PDO::PARAM_INT);
        $stmt->execute();
        
    }
    
    //編集機能
    if(!empty($_POST['edit'])) {
        $edit_num = $_POST['edit_num'];
        
        $sql = "SELECT * FROM message_board1 WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $edit_num, PDO::PARAM_INT);
        $stmt -> execute();
        //配列で取得
        $rows = $stmt->fetch();
        
        if($rows['id']==$edit_num) {
            $editNum = $rows['id'];
            $editName = $rows['name'];
            $editComment = $rows['comment'];
            $editDate = $rows['date'];
        }
	
    }
?>	
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>
<body>
    <form method="POST" action="">
        名前：<input type="text" name="name"  
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($editName, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>"><br>
        コメント：<input type="text" name="comment" 
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($editComment, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>">
        <input type="hidden" name="num_thrown" 
        value="<?php if(!empty($_POST['edit'])) {?>
        <?php echo htmlspecialchars($edit_num, ENT_QUOTES, 'UTF-8');?>
        <?php } ?>">
        <input type="submit" name="submit"><br>
    </form>
    <form method="POST" action="">
        削除番号：<input type="number" name="del_num" >
        <input type="submit" name="delete" value="削除"><br>
    </form>
    <form method="POST" action="">
        編集番号：<input type="number" name="edit_num" >
        <input type="submit" name="edit" value="編集"><br><br>
    </form>
    <?php 
        $sql = 'SELECT * FROM message_board1 ORDER BY id DESC';
	    $stmt = $pdo->query($sql); //$sqlの指示をqueryメソッドで命令する
	    $results = $stmt->fetchAll();
	    foreach($results as $row) {
		    //$rowの中にはテーブルのカラム名が入る
		    echo $row['id'].',';
		    echo $row['name'].',';
		    echo $row['comment'].',';
		    echo $row['date'].'<br>';
	        echo "<hr>";
        }
    ?>
</body>    
</html>
