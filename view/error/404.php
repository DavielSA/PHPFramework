<?php use phpframework\Routers\RouterClass; ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php RouterClass::Style("MyStyle"); ?>
</head>
<body>

    <h1>404 - Page Not Found</h1>
    <p>Sorry, but the page you were trying to view does not exist.</p>
    
    <?php RouterClass::JS("MyJS"); ?>
</body>
</html>
<!-- IE needs 512+ bytes: http://blogs.msdn.com/b/ieinternals/archive/2010/08/19/http-error-pages-in-internet-explorer.aspx -->