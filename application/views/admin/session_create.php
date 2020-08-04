<?php echo $status; ?>
<?php echo form_open(uri_string().'?param='.urlencode($param), 'onsubmit="return window.confirm(\'Apakah anda yakin?\')"'); ?>
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">ID sesi:</label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" style="font-family: monospace" name="session_id" value="(otomatis)" readonly></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Materi:</label></div>
            <div class="col-9 col-sm-12">
                <select class="form-select" name="course_id">
                    <?php foreach ($courses as $course) { ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php echo $course['course_id'] === set_value('course_id') ? 'selected' : ''; ?>><?php echo $course['title']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Peserta:</label></div>
            <div class="col-9 col-sm-12">
                <select class="form-select" name="user_id">
                    <?php foreach ($users as $user) { ?>
                        <option value="<?php echo $user['user_id']; ?>" <?php echo $user['user_id'] === set_value('user_id') ? 'selected' : ''; ?>><?php echo $user['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
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

