<?php echo $status; ?>
<div style="margin-bottom: 1rem">
    <div class="columns">
        <div class="column">
            <fieldset>
                <legend>PENCARIAN</legend>
                <form>
                    <div class="columns">
                        <div class="column col-6 col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon">Materi:</span>
                                <select class="form-select" name="course_id">
                                    <?php foreach ($courses as $course) { ?>
                                        <option value="<?php echo $course['course_id']; ?>" <?php echo $course_id === $course['course_id'] ? 'selected' : ''; ?>><?php echo $course['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="column col-6 col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon">Peserta:</span>
                                <select class="form-select" name="user_id">
                                    <?php foreach ($users as $user) { ?>
                                        <option value="<?php echo $user['user_id']; ?>" <?php echo $user_id === $user['user_id'] ? 'selected' : ''; ?>><?php echo $user['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="column col-5 col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon">Status:</span>
                                <select class="form-select" name="state">
                                    <?php foreach ($states as $state_desc) { ?>
                                        <option value="<?php echo $state_desc['id']; ?>" <?php echo $state_desc['text']; ?> <?php echo $state === $state_desc['id'] ? 'selected' : ''; ?>><?php echo $state_desc['text']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="column col-2 col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">Hal.</span>
                                <select class="form-select" name="page">
                                    <?php for ($i = 1; $i <= $sessions['max_page']; $i++) { ?>
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
                    <div class="column col-auto px-1"><a class="btn btn-primary" href="<?php echo site_url('admin/session_create').'?param='.urlencode($param); ?>">Tambah sesi <i class="icon icon-plus ml-1"></i></a></div>
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
                <th>Materi</th>
                <th>Peserta</th>
                <th>Status</th>
                <th>Waktu Memulai</th>
                <th>Nilai</th>
                <th>Keterangan</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sessions['data'] as $session) { ?>
                <tr>
                    <td><code><?php echo $session['session_id']; ?></code></td>
                    <td><a target="_blank" href="<?php echo site_url('admin/course_edit').'?id='.urlencode($session['course_id']); ?>"><?php echo ellipsize($session['title'], 50); ?></a></td>
                    <td><a target="_blank" href="<?php echo site_url('admin/user_edit').'?id='.urlencode($session['user_id']); ?>"><?php echo ellipsize($session['name'], 50); ?></a></td>
                    <td><b><?php echo array_values(array_filter($states, function($item) use($session) { return $item['id'] === $session['state']; }))[0]['text']; ?></b></td>
                    <td><span class="ezcbt-start-time" data-time="<?php echo $session['start_time']; ?>"></span></td>
                    <td><?php echo $session['state'] === 'finished' ? $session['score'] : 'N/A'; ?></td>
                    <td><?php echo $session['state'] === 'finished' ? $session['details'] : 'N/A'; ?></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-error" onclick="return window.confirm('Apakah anda yakin?')" href="<?php echo site_url('admin/session_delete').'?id='.urlencode($session['session_id']).'&param='.urlencode($param); ?>"><i class="icon icon-delete"></i></a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('.ezcbt-start-time').each(function(index, elem) {
            var txt = 'N/A';
            var time = $(elem).attr('data-time');
            if (time > 0) {
                var d = new Date(time * 1000);
                txt = d.toString();
            }
            $(elem).text(txt);
        });
    });
</script>

