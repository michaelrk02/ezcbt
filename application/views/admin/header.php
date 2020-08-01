<html>
    <head>
        <title>ezCBT Admin<?php echo !empty($title) ? ' - '.$title : ''; ?></title>
        <script src="<?php echo site_url('content').'?type=text%2Fjavascript&path=public%2Fjquery.min.js&cache=86400'; ?>"></script>
        <script src="<?php echo site_url('content').'?type=text%2Fjavascript&path=public%2Fstyles.app.js&cache=86400'; ?>"></script>
        <meta name="viewport" content="width=device-width,initial-scale=1">
    </head>
    <body>
        <div class="app-container">
            <div class="app-content">
                <header class="columns bg-secondary" style="margin: 0; box-shadow: 0px 0px 16px lightgray">
                    <div class="column bg-primary" style="padding: 1rem">
                        <h3>ezCBT Backend</h3>
                        <h5>Admin Page</h5>
                    </div>
                    <?php if (!empty($_SESSION['ezcbt_admin'])) { ?>
                        <div class="column col-auto" style="padding: 1rem; margin-top: auto; margin-bottom: auto">
                            <div class="dropdown dropdown-right">
                                <button type="button" class="btn btn-lg btn-secondary s-circle dropdown-toggle"><i class="icon icon-people"></i></button>
                                <ul class="menu">
                                    <li class="menu-item"><b>ADMIN</b></li>
                                    <li class="divider" data-content="ACTIONS"></li>
                                    <li class="menu-item"><a href="<?php echo site_url('admin/dashboard'); ?>">Dashboard</a></li>
                                    <li class="menu-item"><a href="<?php echo site_url('admin/course_manage'); ?>">Kelola Materi</a></li>
                                    <li class="menu-item"><a href="<?php echo site_url('admin/user_manage'); ?>">Kelola Peserta</a></li>
                                    <li class="menu-item"><a href="<?php echo site_url('admin/session_manage'); ?>">Kelola Sesi</a></li>
                                    <li class="menu-item"><a href="<?php echo site_url('admin/view_results'); ?>">Tinjau Hasil</a></li>
                                    <li class="menu-item"><a onclick="return window.confirm('Apakah anda yakin ingin keluar?')" href="<?php echo site_url('admin/logout'); ?>" class="text-error">Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </header>
                <div style="margin: 1rem">
                    <?php if (!empty($title)) { ?>
                        <h3><?php echo $title; ?></h3>
                    <?php } ?>

