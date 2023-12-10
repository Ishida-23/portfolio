<?php declare(strict_types=1); ?>
<?php require_once dirname(__FILE__) . "/chat_function.php"; ?><!-- 外部ファイル読み込み -->
<?php
session_start();// セッションの開始
$err="";
if(isset($_POST["logout"])){
    Logout();
    header("Location:question.php",true,307);
    exit;
}
if(!isset($_SESSION["loginflg"])){
    header("Location:index.php?page=" .$_GET["page"],true,307);
    exit;
}

$userId=Login();
if($_SERVER["REQUEST_METHOD"]=="POST"){
    // POST確認
    if(isset($_POST["register"])){
        if(!$_POST["text"]==""){
        try{
            $question = preg_replace("/\s|　/", "", $_POST["text"]); //すべての空白除去
            $question=htmlspecialchars($question,ENT_QUOTES | ENT_HTML5);
            $date=date("YmdHis");

            $pdo= Connect();
            $sql="INSERT INTO question";
            $sql.="(userId,question,date)";
            $sql.="VALUES";
            $sql.="(:userId,:question,:date)";

            $statement=$pdo->prepare($sql);
            $statement->bindValue(":userId", $userId ,PDO::PARAM_INT);
            $statement->bindValue(":question", $question ,PDO::PARAM_STR);
            $statement->bindValue(":date", $date ,PDO::PARAM_INT);
            $statement->execute();
            if(isset($_GET["page"])){
                header("Location:question.php",true,307);
                exit;
            }else{
            header("Location:question.php",true,307);
            exit;
            }
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
        }
    }else{
        $err="コメントを入力してください。";
    }
    }
}        
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>質問を投稿</title>
</head>

<body>
    <header>
        <h1><a href="question.php">チャットアプリ</a></h1>
        <?php require_once dirname(__FILE__) . "/header.php"; ?><!-- 外部ファイル読み込み -->
        <link rel="stylesheet" href="css/style.css"><!-- 外部ファイル読み込み -->
    </header>

    <main>
        <h1>質問を投稿する</h1>
        <form action="<?php $_SERVER["PHP_SELF"]?>" method="POST">
            <input type="textarea" name="text" value="" maxlenght="256">
            <input type="submit"name="register" value="登録">
        </form>
        <p><?= $err ?></p>
        <form action= "index.php" method= "GET">
            <input type= "submit" value= "戻る">
        </form>

    </main>
</body>
</html>