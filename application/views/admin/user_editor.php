<?php echo $_ezcbt_status; ?>
<?php echo form_open(uri_string().(isset($user_id) ? '?id='.$user_id : ''), 'onsubmit="return window.confirm(\'Apakah anda yakin?\')"'); ?>
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">ID peserta:</label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" name="user_id" style="font-family: monospace" value="<?php echo isset($user_id) ? $user_id : '(otomatis)'; ?>" readonly></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Nama peserta: <span class="text-error">*</span></label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" name="name" value="<?php echo $name; ?>" placeholder="Masukkan nama peserta ..." required></div>
        </div>
        <div style="margin-top: 1rem">
            <div class="col-9 col-sm-12 col-ml-auto">
                <div class="columns">
                    <div class="column col-auto"><button type="submit" class="btn btn-success" name="submit" value="1">Kirim <i class="icon icon-check ml-1"></i></button></div>
                </div>
            </div>
        </div>
    </div>
</form>

