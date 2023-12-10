<?php declare(strict_types=1); ?>
<!-- 外部ファイル読み込み --><?php require_once dirname(__FILE__) . "/chat_function.php"; ?>
<?php
$err="";
session_start();// セッションの開始

if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST["logout"])){
        Logout();
        header("Location:question.php",true,307);
        exit;
    }elseif(isset($_POST["delete"])){
        Delete_answer();
    }
}

if(!isset($_SESSION["loginflg"])){
    header("Location:index.php?page=" .$_GET["page"]. "&questionId=" .$_GET["questionId"],true,307);
    exit;
}

// 質問文を抽出
$statement;
try{
    // DBに接続
    $pdo=Connect();
    // クエリの準備
    $sql="SELECT name,question,date,question.id FROM question
        LEFT JOIN user ON question.userId = user.id
        WHERE question.id = :p_name";
    // ステートメントの準備
    $statement= $pdo->prepare($sql);
    // 値のバインド
    $statement->bindValue(":p_name",$_GET["questionId"],PDO::PARAM_INT);
    // 実行
    $statement->execute();
}catch(PDOExcetion $ex){   
    $mes  =  "<p>DB接続に失敗しました。<br>";
    $mes .="システム管理者へ連絡してください。</p>";
}

$userId=Login();
if($_SERVER["REQUEST_METHOD"]=="POST"){
    // POST情報からログイン確認
    if(isset($_POST["answer"])){
        try{
            $ans=htmlspecialchars($_POST["text"],ENT_QUOTES | ENT_HTML5);
            $date=date("YmdHis");
            $pdo= Connect();
            $sql="INSERT INTO answer";
            $sql.="(questionId,userId,answer,date)";
            $sql.="VALUES";
            $sql.="(:questionId,:userId,:answer,:date)";
            $answer=$pdo->prepare($sql);
            $answer->bindValue(":questionId", $_GET["questionId"] ,PDO::PARAM_INT);
            $answer->bindValue(":userId", $userId ,PDO::PARAM_INT);
            $answer->bindValue(":answer", $ans ,PDO::PARAM_STR);
            $answer->bindValue(":date", $date ,PDO::PARAM_INT);
            $answer->execute();
            header("Location:question.php",true,307);
            exit;
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
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
    <h2>質問</h2>
    <div>
    <p>
        <?php
            if(isset($statement)) {
                $question= $statement->fetch(PDO::FETCH_ASSOC);
                $questionId =$question["id"];
                $questionQuestion = htmlspecialchars($question["question"],ENT_QUOTES | ENT_HTML5);
                echo $question["name"]."<br>";
                echo $questionQuestion."<br>";
                echo $question["date"]."<br>";
            }
        ?>
    </p>
    </div>

    <h2>回答を投稿する</h2>
    <div>
        <form action="<?php $_SERVER["PHP_SELF"]?>" method="POST">
            <input type="textarea" name="text" value="" maxlenght="256">
            <input type="submit"name="answer" value="投稿">
        </form>
    </div>
    <p><?=$err?></p>
    </main>
</body>
</html>