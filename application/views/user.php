<html>
    <head>
        <title>Computer-Based Test</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <script src="<?php echo site_url('content?type=text%2Fjavascript&name=styles.app.js&cache=86400'); ?>"></script>
        <script src="<?php echo site_url('content?type=text%2Fjavascript&name=vendors-user.app.js&cache=86400'); ?>"></script>
        <script src="<?php echo site_url('content?type=text%2Fjavascript&name=user.app.js'); ?>"></script>
    </head>
    <body>
        <div id="app" data-rpc="<?php echo site_url('rpc'); ?>" data-basename="<?php echo $basename; ?>"></div>
    </body>
</html>
