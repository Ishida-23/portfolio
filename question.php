<?php declare(strict_types=1); ?>
<?php require_once dirname(__FILE__)."/chat_function.php";?><!-- 外部ファイル読み込み -->
<?php
session_start();// セッションの開始
$err="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST["logout"])){
        Logout();
    }elseif(isset($_POST["delete"])){
        Delete_question();
    }
}

$statement;
try{
    // DBに接続
    $pdo= Connect();
    // クエリの準備
    $sql="SELECT question.id,question,date,name,userId FROM question 
        LEFT JOIN user ON question.userId=user.id 
        WHERE deleteflg!=1 ORDER BY question.id DESC;";
    // スタートメントの準備
    $statement=$pdo->prepare($sql);
    // 実行
    $statement->execute();
}catch(PDOException $ex){
    $err= "接続に失敗しました。";
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャットアプリ</title>
    <link rel="stylesheet" href="css/style.css"><!-- 外部ファイル読み込み -->
</head>
<body>
    
    <header>
        <h1><a href= "question.php">チャットアプリ</a></h1>
        <nav>
            <?php require_once dirname(__FILE__) . "/header.php"; ?><!-- 外部ファイル読み込み -->
            <form action= "questionInput.php" method= "GET">
                <input type= "hidden" name= "page" value= "questionInput.php">
                <input type= "submit"  value= "質問を投稿する">
            </form>
        </nav>
</header>
    <main>
    <?php
        // PDO::FETCH_ASSOCはDBから該当したカラム名のみ取得
        while($question = $statement ->fetch(PDO::FETCH_ASSOC)){
            $questionId= $question["id"];
            $question_text= htmlspecialchars($question["question"],ENT_QUOTES | ENT_HTML5);
        ?>  
            <div class= "question">
            <p>
                <?= $question["name"] ?><br>
                <?= $question_text ?><br>
                <?= $question["date"] ?><br>
            </p>
                <div class= "button">
                    <form action= "detail.php" method= "GET">
                        <input type= "hidden" name= "questionId" value= "<?= $questionId?> ">
                        <input type= "submit" name="" value= "詳細">
                    </form>
    
             <!-- 本人のみ削除 -->
    <?php       if(Login() == $question["userId"]){ ?>
                    <form action= "<?= $_SERVER['PHP_SELF'] ?>" method= 'POST'>
                        <input type= "hidden" name= "questionId" value= "<?= $questionId ?> ">
                        <input type= "submit" name= "delete" value= "削除">
                    </form>
            

    <?php   
                }
            echo "</div>";
        echo "</div>";
        }
    ?>
    <p><?= $err ?></p>

    </main>
</body>
</html>