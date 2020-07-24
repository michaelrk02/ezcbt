<?php echo form_open('admin/login'); ?>
    <p>Masuk terlebih dahulu untuk mengakses: <b><?php echo $redirect; ?></b></p>
    <?php echo $status; ?>
    <div class="input-group">
        <span class="input-group-addon">Password:</span>
        <input type="password" class="form-input" name="password" placeholder="Masukkan password ...">
        <button class="btn btn-success input-group-btn" name="login" value="1">Masuk</button>
    </div>
</form>

