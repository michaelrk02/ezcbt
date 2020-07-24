<div class="columns">
    <div class="column col-4 col-md-6 col-sm-12">
        <div class="empty" style="margin: 0.5rem; box-shadow: 0px 0px 8px lightgray">
            <div class="empty-icon">
                <i class="icon icon-copy icon-4x"></i>
            </div>
            <h5 class="empty-title">Jumlah materi tes: <?php echo $num_courses; ?></h5>
            <p class="empty-subtitle">Sebanyak <?php echo $num_courses; ?> materi tes telah terdaftar oleh sistem (<?php echo $num_courses_locked; ?> di antaranya terkunci)</p>
            <div class="empty-action">
                <a class="btn btn-primary" href="<?php echo site_url('course_manage'); ?>">Kelola</a>
            </div>
        </div>
    </div>
    <div class="column col-4 col-md-6 col-sm-12">
        <div class="empty" style="margin: 0.5rem; box-shadow: 0px 0px 8px lightgray">
            <div class="empty-icon">
                <i class="icon icon-people icon-4x"></i>
            </div>
            <h5 class="empty-title">Jumlah peserta tes: <?php echo $num_users; ?></h5>
            <p class="empty-subtitle">Sebanyak <?php echo $num_users; ?> peserta telah terdaftar oleh sistem</p>
            <div class="empty-action">
                <a class="btn btn-primary" href="<?php echo site_url('user_manage'); ?>">Kelola</a>
            </div>
        </div>
    </div>
    <div class="column col-4 col-md-6 col-sm-12">
        <div class="empty" style="margin: 0.5rem; box-shadow: 0px 0px 8px lightgray">
            <div class="empty-icon">
                <i class="icon icon-time icon-4x"></i>
            </div>
            <h5 class="empty-title">Jumlah sesi tes: <?php echo $num_sessions; ?></h5>
            <p class="empty-subtitle">Sebanyak <?php echo $num_sessions; ?> sesi tes telah terdaftar oleh sistem</p>
            <div class="empty-action">
                <a class="btn btn-primary" href="<?php echo site_url('session_manage'); ?>">Kelola</a>
            </div>
        </div>
    </div>
    <div class="column col-4 col-md-6 col-sm-12">
        <div class="empty" style="margin: 0.5rem; box-shadow: 0px 0px 8px lightgray">
            <div class="empty-icon">
                <i class="icon icon-check icon-4x"></i>
            </div>
            <h5 class="empty-title">Jumlah sesi selesai: <?php echo $num_results; ?></h5>
            <p class="empty-subtitle">Sebanyak <?php echo $num_results; ?> peserta telah menyelesaikan sesinya</p>
            <div class="empty-action">
                <a class="btn btn-primary" href="<?php echo site_url('view_results'); ?>">Cek hasil</a>
            </div>
        </div>
    </div>
</div>

