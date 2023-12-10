<?php declare(strict_types=1); ?>
<!-- 外部ファイル読み込み --><?php require_once dirname(__FILE__) . "/chat_function.php"; ?>
<?php
session_start();// セッションの開始
$err="";
$questionInput = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $questionInput = $_POST["questionId"];
    if(isset($_POST["logout"])){
        Logout();
        header("Location:question.php",true,307);
        exit;
    }elseif(isset($_POST["delete"])){
        Delete_answer();
    }
}else{
    if(isset($_GET["questionId"])){             
        $questionInput = $_GET["questionId"];
    }else{
        header("Location:question.php",true,307);
        exit;
    }
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
    $statement->bindValue(":p_name", $questionInput ,PDO::PARAM_INT);
    // 実行
    $statement->execute();
}catch(PDOExcetion $ex){   
    $err  =  "<p>DB接続に失敗しました。<br>";
    $err .="システム管理者へ連絡してください。</p>";
}
?>

<?php
//回答表示
$statementAns;
try{
    // DBに接続
    $pdo=Connect();
    // クエリの準備
    $sql="SELECT name,answer,date,answer.id,userId,questionId FROM answer
            LEFT JOIN user  ON answer.userId = user.id
            WHERE deleteFlg != 1 AND answer.questionId = :a_name";
    // ステートメントの準備
    $statementAns= $pdo->prepare($sql);
    // 値のバインド
    $statementAns->bindValue(":a_name", $questionInput ,PDO::PARAM_INT);
    // 実行
    $statementAns->execute();
}catch(PDOExcetion $ex){
    $err  =  "<p>DB接続に失敗しました。<br>";
    $err .="システム管理者へ連絡してください。</p>";
}
?>

<!DOCTYPE html>
<html lang="ja">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>質問詳細画面</title>
    <link rel="stylesheet" href="css/style.css"><!-- 外部ファイル読み込み -->
</head>

<body>
    <header> <!--ヘッダー　-->
        <h1><a href="question.php">チャットアプリ</a></h1>
        <?php require_once dirname(__FILE__) . "/header.php"; ?><!-- 外部ファイル読み込み -->
    </header> <!--ヘッダーここまで　-->

    <main>
        <p>質問</p>
            <div class="question">
                <form action="answer.php" method="GET">
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
                    <input type="hidden" name="questionId" value="<?=$questionId ?>" >
                    <input type="hidden" name="page" value="answer.php">
                    <input type="submit" name="answer" value="回答">                    
                </form>
            </div>
        <P>回答</P>
        <?php
        // 回答文をフェッチ
        while($ans= $statementAns->fetch(PDO::FETCH_ASSOC)){
            $answerId =$ans["id"];
            $answerAns = htmlspecialchars($ans["answer"],ENT_QUOTES | ENT_HTML5);
            $answerDate =$ans["date"];
            $questionId=$ans["questionId"];
        ?>
            <div class="answer">
                <p>
                    <?=$ans["name"]?><br>
                    <?=$answerAns?><br>
                    <?=$answerDate?><br>
                </p>
        <?php   if(Login() == $ans["userId"]){  ?> <!-- 本人のみ削除 -->
                    <form action="<?=$_SERVER['PHP_SELF']?> "method='POST'>
                        <input type="hidden" name= "questionId" value= "<?= $questionId ?>">
                        <input type="hidden" name= "answerId" value= "<?= $answerId ?>">
                        <input type="submit" name= "delete" value= "削除">
                    </form>
        <?php
                }
            echo "</div>";
        }
        ?>

        <!-- 質問一覧に戻る -->
        <form action= "question.php" method= "GET">
            <input type= "submit" value= "戻る">
        </form>
        
    </main> <!--メインここまで　-->
</body>
</html>