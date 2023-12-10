<?php declare(strict_types=1); ?>
<!-- 外部ファイル読み込み --><?php require_once dirname(__FILE__) . "/chat_function.php"; ?>
<?php
session_start();// セッションの開始
$err="";
$page="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
// POST情報からログイン確認
    if($_POST["userId"] !="" && $_POST["userPw"] !=""){
        if(isset($_POST["login"])){
            try{
                $pdo= Connect();
                $sql="SELECT * FROM user WHERE loginId= :id";
                $statement=$pdo->prepare($sql);
                $statement->bindValue(":id",$_POST["userId"],PDO::PARAM_INT);
                $statement->execute();
            }catch(PDOException $ex){
                $err= "接続に失敗しました。";
            }
                if(isset($statement)) {
                    $Pw= $statement->fetch(PDO::FETCH_ASSOC);  
                    if($_POST["userPw"]==$Pw["password"]){
                    // ログイン成功時
                        // セッションにIDとパスワード、フラグを取得
                        $_SESSION["userId"]=$_POST["userId"];
                        $_SESSION["userPw"]=$_POST["userPw"];
                        $_SESSION["loginflg"]=True;
                        // 遷移先
                        Redirect();
                    }else{
                        $err="ユーザーIDまたはパスワードがまちがっています。";
                    }
                }   
        }else{
            $err="入力してください。";
        }
    }
}


if(isset($_SESSION["loginflg"])){
    header("Location:question.php",true,307);
    exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="css/style.css"><!-- 外部ファイル読み込み -->
</head>

<body>
    <header>
        <h1><a href="question.php">チャットアプリ</a></h1>
    </header>

    <main>
        <h2>ログイン</h2>
        
        <form action="<?php $_SERVER["PHP_SELF"]?>" method="POST">
            <?= $err ?>
            <p>ユーザーID:<input type="text" name="userId"  maxlength="10" value=""></p>
            <p>パスワード:<input type="password" name="userPw" maxlength="10" value=""></p>
            <input type="hidden" name="page" value="<?=$page ?>">
            <input type="submit" name="login" value="ログイン">
        </form>

        <form action="userAdd.php" method="GET">
            <input type="hidden" name="page" value="<?=$page ?>"> 
            <input type="submit" name="" value="新規登録">
        </form>

    </main>
</body>
</html>