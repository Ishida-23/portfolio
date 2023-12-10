<?php declare(strict_types=1); ?>
<?php
date_default_timezone_set('Asia/Tokyo'); //日本のタイムゾーンに設定

// DB接続
function Connect():PDO{
    $pdo= new PDO(
        // 接続先DB情報,文字コード
        "mysql:host=127.0.0.1:3306; dbname=ideastock;charset=utf8mb4",
        // DBログインID
        "root",
        // DBログインパスワード
        "pass");
    // PDO::ATTR_ERRMODEという属性でPDO::ERRMODE_EXCEPTIONの値を設定することでエラーが発生したときに、
    // PDOExceptionの例外を投げる。
    $pdo->SetAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // SQLインジェクション対策
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    return $pdo;
}

// 返り値userテーブル　idカラム名
function Login(){
    if(isset($_SESSION["userId"]) && isset($_SESSION["userPw"])){
        try{
            $pdo= Connect();
            $sql= "SELECT id FROM user WHERE loginId= :id";
            $login= $pdo->prepare($sql);
            $login->bindValue(":id",$_SESSION["userId"],PDO::PARAM_INT);
            $login->execute();
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
        }
        $id = $login->fetch(PDO::FETCH_ASSOC);
        $userId= $id["id"];
        return $userId;
    }else{
        $err="ログインできていません。";
    }
}

// ログアウト
function Logout(){
    $_SESSION=array();
    session_destroy();
}

// 遷移先
function Redirect(){
    if(isset($_GET["page"]) && isset($_GET["questionId"])){
        header("Location:" .$_GET['page']. "?questionId=" .$_GET["questionId"],true,307); //answer.php
        exit;
    }elseif(isset($_GET["page"])){
        header('Location:' .$_GET['page'],true,307);//question.php
        exit;
    }else{
        header("Location:question.php",true,307);//通常ログイン時
        exit;
    }
}
// ログイン判定
function Loginflg(){
    if(isset($_SESSION["loginflg"])){
        header("Location:question.php",true,307);
        exit;
    }
}

// 削除操作 
function Delete_question(){
    try{
        $pdo= Connect();
        // 確認していない
        $sql= "UPDATE question SET deleteflg = 1 WHERE id = :questionId ";
        $delete= $pdo->prepare($sql);
        $delete->bindValue(":questionId",$_POST["questionId"],PDO::PARAM_INT);
        $delete->execute();
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
        }
}

function Delete_answer(){
    try{
        $pdo= Connect();
        $sql= "UPDATE answer SET deleteFlg = 1 WHERE id = :answerId";
        $delete= $pdo->prepare($sql);
        $delete->bindValue(":answerId",$_POST["answerId"],PDO::PARAM_INT);
        $delete->execute();
        }catch(PDOException $ex){
            $err= "接続に失敗しました。";
        }
}

?>