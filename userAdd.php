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

// POST情報からアカウント登録
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if($_POST["viewName"] !="" && $_POST["userId"] !="" && $_POST["userPw"] !="" ){
        $viewName = trim($_POST["viewName"]);//前後の空白除去
        $userId = preg_replace("/\s|　/", "", $_POST["userId"]); //すべての空白除去
        $pass = preg_replace("/\s|　/", "", $_POST["pass"]); //すべての空白除去
        $viewName = htmlspecialchars($viewName,ENT_QUOTES | ENT_HTML5);
        $userId = htmlspecialchars($userId,ENT_QUOTES | ENT_HTML5);
        $pass = htmlspecialchars($pass,ENT_QUOTES | ENT_HTML5);
        
        if(strlen($userId) >= 6&& strlen($pass) >= 6){
            // ログイン成功時
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

                //ログイン画面にリダイレクト
                header("Location:index.php",true,307);
                exit;
            }catch(PDOException $ex){
                $err= "接続に失敗しました。";
            }
        }elseif(strlen($userId) <6){
            $err="ユーザー名は6～10文字で登録してください。";
        }else{
            $err="パスワードは6～10文字で登録してください。";
        }
    }else{
        $err="入力してください。";
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

    <header> <!--ヘッダー　-->
        <h1><a href="question.php">チャットアプリ</a></h1>
    </header> <!--ヘッダーここまで　-->

    <main> <!--メイン　-->
        <form action="userAdd.php" method="POST">
            <p>表示名:<input type= "text" name= "viewName"  maxlength= "10" value="" placeholder="最大10文字まで"></p>
            <p>ユーザーID:<input type= "text" name= "userId" maxlength= "10" value="" placeholder="6～10文字で登録してください。"></p>
            <p>パスワード:<input type= "text" name= "pass"  maxlength= "10" value="" placeholder="6～10文字で登録してください。"></p>
            <input type="submit" name= "entry" value= "登録">
            <?= $err ?>
        </form>

        <form action= "index.php" method= "GET">
            <input type= "submit" value= "戻る">
        </form>
    </main> <!--メインここまで　-->
</body>
</html>