<?php echo $status; ?>
<div style="margin-bottom: 1rem">
    <div class="columns">
        <div class="column">
            <fieldset>
                <legend>PENCARIAN</legend>
                <form>
                    <div class="columns">
                        <div class="column col-5 col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon">Judul:</span>
                                <input type="text" class="form-input" name="match" placeholder="Masukkan judul materi ..." value="<?php echo htmlspecialchars($_GET['match']); ?>">
                            </div>
                        </div>
                        <div class="column col-2 col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">Hal.</span>
                                <select class="form-select" name="page">
                                    <?php for ($i = 1; $i <= $courses['max_page']; $i++) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $_GET['page'] == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="column col-3 col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="number" class="form-input" name="items" min="1" value="<?php echo htmlspecialchars($_GET['items']); ?>">
                                <span class="input-group-addon">item</span>
                            </div>
                        </div>
                        <div class="column col-2 col-md-12">
                            <button type="submit" class="btn btn-success btn-block">Cari <i class="icon icon-search ml-1"></i></button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </div>
        <div class="column col-auto col-sm-12">
            <fieldset>
                <legend>TINDAKAN</legend>
                <div class="columns">
                    <div class="column col-auto px-1"><a class="btn btn-primary" href="<?php echo site_url('admin/course_create'); ?>">Tambah materi <i class="icon icon-plus ml-1"></i></a></div>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div style="margin-bottom: 1rem">
    <table class="table table-scroll table-hover table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul Materi</th>
                <th>Terkunci</th>
                <th>Durasi</th>
                <th>Jumlah Soal</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses['data'] as $course) { ?>
                <tr>
                    <td><code><?php echo $course['course_id']; ?></code></td>
                    <td><?php echo ellipsize($course['title'], 50); ?></td>
                    <td><?php echo $course['locked'] == 1 ? '<i class="icon icon-check text-success"></i>' : '<i class="icon icon-cross text-error"></i>'; ?></td>
                    <td><?php echo format_hms(seconds_to_hms($course['duration'])); ?></td>
                    <td><?php echo $course['num_questions']; ?></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-primary" href="<?php echo site_url('admin/course_edit').'?id='.urlencode($course['course_id']); ?>"><i class="icon icon-edit"></i></a>
                            <a class="btn btn-error" href="<?php echo site_url('admin/course_delete').'?id='.urlencode($course['course_id']); ?>"><i class="icon icon-delete"></i></a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

