<?php 
    session_start();
    require_once("SwiftCSRF.php");
    $csrf = new SwiftCSRF();
?>

<form action="test_form_.php" method="post">
    <?php echo $csrf->CreateCsrfInput("hi"); ?>
    <input type="submit">
</form>