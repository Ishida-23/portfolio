<?php if(isset($_SESSION["userId"])):?>
        <form action="<?php $_SERVER["PHP_SELF"]?>" method="POST">
        <input type="submit" name="logout" value="ログアウト">
        </form>
<?php else:?>
        <form action="index.php" method="GET">
        <input type="submit" name="" value="ログイン">
        </form>
<?php endif; ?>