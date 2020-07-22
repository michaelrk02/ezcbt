<html>
    <head>
        <title>Computer-Based Test</title>
        <script src="<?php echo site_url('content?type=text%2Fjavascript&path=public%2Fstyles.app.js&cache=86400'); ?>"></script>
        <script src="<?php echo site_url('content?type=text%2Fjavascript&path=public%2Fvendors-user.app.js&cache=86400'); ?>"></script>
        <script src="<?php echo site_url('content?type=text%2Fjavascript&path=public%2Fuser.app.js'); ?>"></script>
    </head>
    <body>
        <div id="app" data-rpc="<?php echo site_url('rpc'); ?>" data-basename="<?php echo $basename; ?>"></div>
    </body>
</html>
