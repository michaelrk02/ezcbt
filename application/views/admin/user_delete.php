<p>
    Apakah anda yakin ingin menghapus peserta <b><?php echo $name; ?></b>?
    <a href="<?php echo site_url(uri_string()).'?id='.$_GET['id'].'&confirm=1'; ?>" class="btn btn-error">YA <i class="icon icon-check"></i></a>
    <a href="<?php echo site_url('admin/user_manage'); ?>" class="btn btn-success">TIDAK <i class="icon icon-cross"></i></a>
</p>
