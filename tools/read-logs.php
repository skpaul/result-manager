<?php 
    require_once("../Required.php");
    
    require_once("prevent_access_if_not_localhost.php");
    $queryString = $_SERVER['QUERY_STRING'];
    

    Required::SwiftLogger();
    $logger = new SwiftLogger(ROOT_DIRECTORY);

    $iconName= "happy.png";
    if($logger->hasLogs()){
        $iconName= "unhappy.png";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Error Logs</title>
        <link rel="shortcut icon" type="image/png" href="images/<?php echo $iconName; ?>"/>
        <style>
            a{
                display: flex;
                justify-content: center;
                border: 1px solid #9e9393;
                width: 200px;
                margin: auto;
                border-radius: 4px;
                background-color: gray;
                color: white;
                text-decoration: none;
                font-size: 20px;
                font-family: arial, sans-serif;
                letter-spacing: 0.04245em;
                padding: 10px 0;
            }
        </style>
    </head>

    <body>
    <button onClick="window.location.reload();">Refresh Page</button>

    <?php
        if($logger->hasLogs()){
            echo '<a href="clear-logs.php?'.$queryString.'">Clear error logs</a><br><br>';
        }
    ?>
        
        <?php
            try {
                $logger->readLogs();
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
           
        ?>
        <?php
        if($logger->hasLogs()){
            echo '<a href="clear-logs.php?'.$queryString.'">Clear error logs</a><br><br>';
        }
    ?>

        <script>
            setTimeout(function(){
                window.location.reload(1);
            }, 30000);
        </script>
        
    </body>
</html>