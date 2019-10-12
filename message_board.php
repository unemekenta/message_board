<html>
 <html lang = "ja">
<head>
  <meta charset = "utf-8">
</head>

<body>

  <?php
  // DB関連
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS board"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATE,"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);

  // 確認用コード
    // $sql ='SHOW TABLES';
    // $result = $pdo -> query($sql);
    // foreach ($result as $row){
    //   echo $row[0];
    //   echo '<br>';
    // }
    // echo "<hr>";

    // $sql ='SHOW CREATE TABLE board';
    // $result = $pdo -> query($sql);
    // foreach ($result as $row){
    //   echo $row[1];
    // }
    // echo "<hr>";
  // 確認用コード終了

  // DB関連終了

    $name = "";
    $comment = "";
    $pass = "pass";
    $edit_name = "";
    $edit_comment = "";
    $edit_number = "";
    $date = "";

  //入力モード
    if (!empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['editedNo'])){
      if (!empty($_POST['pass'])){
        $sql = $pdo -> prepare("INSERT INTO board (name, comment, date) VALUES (:name, :comment, :date)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $date = date("Y/m/d H:i:s");
        $sql -> execute();

        echo "メッセージを送信しました";
      }else{
        echo "正しいパスワードを入れてください";
      }
    }elseif(!empty($_POST['editedNo']) && !empty($_POST['name']) && !empty($_POST['comment'])){
      if (($_POST['pass']) == $pass){
        $id =  $_POST["editedNo"];
        $name =  $_POST["name"];
        $comment =$_POST["comment"];
        $sql = 'update board set name=:name,comment=:comment where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "メッセージを編集しました";
      }else{
        echo "正しいパスワードを入れてください";
      }
    }

    elseif(!empty($_POST['deleteNo'])){
      if (($_POST['deletepass']) == $pass){
        $id = $_POST["deleteNo"];
        $sql = 'delete from board where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "メッセージを削除しました";
      }else{
        echo "正しいパスワードを入れてください";
      }
    }


    elseif(!empty($_POST['editNo'])){
      if(($_POST['editpass']) == $pass){
        $id =  $_POST["editNo"]; //変更する投稿番号

        $stmt1 = $pdo->prepare("SELECT name FROM board WHERE id = $id");
        $stmt1->execute();
        $edit_name = $stmt1->fetchColumn();

        $stmt2 = $pdo->prepare("SELECT comment FROM board WHERE id = $id");
        $stmt2->execute();
        $edit_comment = $stmt2->fetchColumn();

        $stmt3 = $pdo->prepare("SELECT id FROM board WHERE id = $id");
        $stmt3->execute();
        $edit_number = $stmt3->fetchColumn();
      }
    }

    else{
      echo "エラー";
    }
  ?>
  <!--送信用の入力フォームを設置-->
  <form method= "post" action="">
  <input type="text" name="name" value="<?php echo $edit_name;?>" placeholder="名前"><br>
  <input type="text" name="comment" value="<?php echo $edit_comment;?>" placeholder="メッセージ"><br>
  <input type="password" name="pass" value="" placeholder="パスワード"><br>
  <input type="hidden" name="editedNo" value="<?php echo $edit_number;?>"><br>
  <input type="submit">
  </form>
  <br>

  <!--削除用の入力フォームを設置-->
  <form method= "post" action="">
  <input type="text" name="deleteNo" placeholder="削除対象番号"><br>
  <input type="password" name="deletepass" value="" placeholder="パスワード"><br>
  <input type="submit" name="delete" value="削除">
  </form>
  <br>

  <!--編集用の入力フォームを設置-->
  <form method= "post" action="">
  <input type="text" name="editNo" placeholder="編集対象番号"><br>
  <input type="password" name="editpass" value="" placeholder="パスワード"><br>
  <input type="submit" name="edit" value="編集">
  </form>
  <br>

  <?php
    $sql = 'SELECT * FROM board';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
      echo 'ID：'.$row['id'].'　';
      echo 'Name：'.$row['name'].'　';
      echo 'Message：'.$row['comment'].'　';
      echo 'Date：'.$row['date'].'<br>';
      echo "<hr>";
    }
  ?>

</body>
</html>
