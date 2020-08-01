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
                                <span class="input-group-addon">Nama:</span>
                                <input type="text" class="form-input" name="match" placeholder="Masukkan nama peserta ..." value="<?php echo htmlspecialchars($_GET['match']); ?>">
                            </div>
                        </div>
                        <div class="column col-2 col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">Hal.</span>
                                <select class="form-select" name="page">
                                    <?php for ($i = 1; $i <= $users['max_page']; $i++) { ?>
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
                    <div class="column col-auto px-1"><a class="btn btn-primary" href="<?php echo site_url('admin/user_create'); ?>">Tambah peserta <i class="icon icon-plus ml-1"></i></a></div>
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
                <th>Nama</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users['data'] as $user) { ?>
                <tr>
                    <td><code><?php echo $user['user_id']; ?></code></td>
                    <td><?php echo ellipsize($user['name'], 50); ?></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-primary" href="<?php echo site_url('admin/user_edit').'?id='.urlencode($user['user_id']); ?>"><i class="icon icon-edit"></i></a>
                            <a class="btn btn-error" href="<?php echo site_url('admin/user_delete').'?id='.urlencode($user['user_id']); ?>"><i class="icon icon-delete"></i></a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

