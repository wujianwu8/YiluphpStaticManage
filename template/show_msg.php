<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv=Content-Type content="text/html;charset=utf-8">
    <title><?php echo $msg.'('.$err_code.')'; ?></title>
</head>
<body>
<main role="main" class="container mt-3">
    <div class="row">
        <div class="text-center col-12 pt-5 pb-5 content-bg">
            <?php if($err_code===-1): ?>
            <script>
                alert("<?php echo htmlspecialchars($msg); ?>");
                document.location.href = "<?php echo $config['user_center']['host'] ?>/?redirect_uri="+encodeURIComponent(document.location.href);
            </script>
            <?php endif; ?>

            <p><?php echo $msg; ?></p>

            <p>ERROR CODE(<?php echo $err_code; ?>)</p>
        </div>
    </div>
</main>
</body>
</html>