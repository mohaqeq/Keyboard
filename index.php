<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        include ("KeyboardTraverser.php");
        $k = new KeyboardTraverser();
        $p = $k->getKeySequence("This is a test. We type some characters like #, &, :, / and | for TEST.");
        echo "<ol>";
        foreach ($p as $s) {
            echo "<li>";
            foreach ($s as $value) {
                echo $value;
                echo "<br/>";
            }
            echo "</li>";
        }
        echo "</ol>";
        ?>
    </body>
</html>
