<?php declare(strict_types=1); ?>
<!-- 外部ファイル読み込み --><?php require_once dirname(__FILE__) . "/chat_function.php"; ?>
<?php
session_start();// セッションの開始
$err="";
$page="";

if(isset($_SESSION["loginflg"])){
    header("Location:question.php",true,307);
    exit;
}

// POST情報からログイン確認
if(isset($_POST["entry"])){
    $viewName=htmlspecialchars($_POST["viewName"],ENT_QUOTES | ENT_HTML5);
    $userId=htmlspecialchars($_POST["userId"],ENT_QUOTES | ENT_HTML5);
    $pass=htmlspecialchars($_POST["pass"],ENT_QUOTES | ENT_HTML5);
    if(strlen($userId) >= 6&& strlen($pass) >= 6){
        try{
            $pdo= Connect();
            $sql="INSERT INTO user";
            $sql.="(loginId,password,name)";
            $sql.="VALUES";
            $sql.="(:loginId,:password,:name)";
            $statement=$pdo->prepare($sql);
            $statement->bindValue(":loginId",$userId,PDO::PARAM_STR);
            $statement->bindValue(":password",$pass,PDO::PARAM_INT);
            $statement->bindValue(":name",$viewName,PDO::PARAM_INT);
            $statement->execute();
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
        }
        if(isset($statement)) {
        // ログイン成功時
            header("Location:index.php",true,307);
            exit;
        }else{
            $err="登録に失敗しました。";
        }
    }elseif(strlen($userId) <6){
        $err="ユーザー名は6～10文字で登録してください。";
    }else{
        $err="パスワードは6～10文字で登録してください。";
    }
}
   
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="css/style.css"><!-- 外部ファイル読み込み -->
</head>

<body>
    <header>
        <h1><a href="question.php">チャットアプリ</a></h1>
    </header>
    <div class="userAdd"></div>
    <form action="userAdd.php" method="POST">
        <p>表示名:<input type="text" name="viewName"  maxlength="10" value=""></p>
        <p>ユーザーID:<input type="text" name="userId" maxlength="10" value=""></p>
        <p>パスワード:<input type="text" name="pass"  maxlength="10" value=""></p>
        <input type="submit" name="entry" value="登録">
        <?= $err ?>
    </form>
    </div>

    <form action="index.php" method="GET">
        <input type="submit" value="戻る">
    </form>

</body>
</html>