<?php global $locator; if(!$locator) exit('Forbidden. hacking attempt'); ?>
<!doctype html>
<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
<body>
<p style="text-align: center;"><?php echo isset($message) ? $message : '' ?></p>
</body>
</html>