<?php echo $_ezcbt_status; ?>
<?php echo form_open_multipart(uri_string().(isset($course_id) ? '?id='.$course_id : ''), 'onsubmit="return window.confirm(\'Apakah anda yakin?\')"'); ?>
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">ID materi:</label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" name="course_id" style="font-family: monospace" value="<?php echo isset($course_id) ? $course_id : '(otomatis)'; ?>" readonly></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Judul materi: <span class="text-error">*</span></label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" name="title" value="<?php echo $title; ?>" placeholder="Masukkan judul materi ..." required></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Deskripsi:</label></div>
            <div class="col-9 col-sm-12"><textarea class="form-input" name="description" rows="5" style="resize: none" placeholder="Masukkan deskripsi materi ..."><?php echo $description; ?></textarea></div>
        </div>
        <div class="form-group">
            <div class="col-9 col-sm-12 col-ml-auto">
                <label class="form-checkbox">
                    <input type="checkbox" name="locked" value="1" <?php echo !empty($locked) ? 'checked' : ''; ?>>
                    <i class="form-icon"></i> Terkunci
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Durasi: <span class="text-error">*</span></label></div>
            <div class="col-9 col-sm-12">
                <div class="input-group">
                    <button type="button" id="__ezcbt_CalcDuration" class="btn btn-primary input-group-btn">Hitung</button>
                    <input type="number" id="__ezcbt_Duration" class="form-input" name="duration" min="0" value="<?php echo $duration; ?>" required>
                    <span class="input-group-addon">detik</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Jumlah soal: <span class="text-error">*</span></label></div>
            <div class="col-9 col-sm-12">
                <div class="columns">
                    <div class="column col-4 col-md-12"><input type="number" class="form-input" name="num_questions" id="__ezcbt_NumQuestions" min="1" value="<?php echo $num_questions; ?>" required></div>
                    <div class="column col-5 col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            <input type="number" class="form-input" name="num_choices" id="__ezcbt_NumChoices" min="2" max="10" value="<?php echo $num_choices; ?>" required>
                            <span class="input-group-addon">pilihan</span>
                        </div>
                    </div>
                    <div class="column col-3 col-md-12"><button type="button" class="btn btn-success btn-block" id="__ezcbt_UpdateQuestions" disabled>Perbarui <i class="icon icon-refresh ml-1"></i></button></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Data jawaban benar:</label></div>
            <div class="col-9 col-sm-12"><input type="text" class="form-input" name="correct_answers" id="__ezcbt_CorrectAnswerData" style="font-family: monospace" value="<?php echo $correct_answers; ?>" readonly></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Tabel jawaban benar:</label></div>
            <div class="col-9 col-sm-12">
                <div id="__ezcbt_AnswerTable" class="columns">
                    <div class="column col-4 col-md-6 __ezcbt_AnswerTemplate" style="display: none">
                        <div class="input-group">
                            <span class="input-group-addon">No. <span class="__ezcbt_AnswerNum"></span></span>
                            <select class="form-select" class="__ezcbt_AnswerChoices">
                                <option></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-9 col-sm-12 col-ml-auto">
                <label class="form-checkbox">
                    <input type="checkbox" name="allow_empty" value="1" <?php echo !empty($allow_empty) ? 'checked' : ''; ?>>
                    <i class="form-icon"></i> Pilihan dapat dikosongi
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Skor: <span class="text-error">*</span></label></div>
            <div class="col-9 col-sm-12">
                <div class="columns">
                    <div class="column col-4 col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">Benar:</span>
                            <input type="number" class="form-input" name="score_correct" value="<?php echo $score_correct; ?>">
                        </div>
                    </div>
                    <div class="column col-4 col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">Kosong:</span>
                            <input type="number" class="form-input" name="score_empty" value="<?php echo $score_empty; ?>">
                        </div>
                    </div>
                    <div class="column col-4 col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon">Salah:</span>
                            <input type="number" class="form-input" name="score_wrong" value="<?php echo $score_wrong; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">File PDF Materi:</label></div>
            <div class="col-9 col-sm-12"><input type="file" class="form-input" name="course_pdf"></div>
        </div>
        <div class="form-group">
            <div class="col-3 col-sm-12"><label class="form-label">Status File:</label></div>
            <div class="col-9 col-sm-12">
                <label class="form-label">
                    <b><?php echo $file_available ? 'TERSEDIA' : 'BELUM TERSEDIA'; ?></b>
                    <?php if ($file_available) { ?>
                        (<a href="<?php echo site_url('admin/course_pdf').'?id='.$course_id; ?>" target="_blank">Lihat file</a>)
                    <?php } ?>
                </label>
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
<script>
    function ezcbtUpdateQuestions() {
        var numQuestions = $('#__ezcbt_NumQuestions').val();
        var numChoices = $('#__ezcbt_NumChoices').val();

        var correctAnswerData = $('#__ezcbt_CorrectAnswerData');
        var str = correctAnswerData.val();

        if (numQuestions > str.length) {
            for (var i = str.length; i < numQuestions; i++) {
                str = str + '0';
            }
        } else if (numQuestions < str.length) {
            str = str.substring(0, numQuestions);
        }
        correctAnswerData.val(str);

        var newAnswerData = '';
        for (var i = 0; i < str.length; i++) {
            if (parseInt(str.charAt(i)) >= numChoices) {
                var newChoice = (numChoices - 1).toString();
                newAnswerData = newAnswerData + newChoice;
            } else {
                newAnswerData = newAnswerData + str.charAt(i)
            }
        }
        correctAnswerData.val(newAnswerData);

        ezcbtExportCorrectAnswers();
    }

    function ezcbtImportCorrectAnswers() {
        var data = '';
        $('.__ezcbt_Answer').each(function(i, elem) {
            data = data + $(elem).find('select').val().toString();
        });
        $('#__ezcbt_CorrectAnswerData').val(data);
    }

    function ezcbtExportCorrectAnswers() {
        $('.__ezcbt_Answer').remove();

        var templ = $('.__ezcbt_AnswerTemplate');
        var data = $('#__ezcbt_CorrectAnswerData').val();
        for (var i = 0; i < data.length; i++) {
            var el = templ.clone();
            el.removeClass('__ezcbt_AnswerTemplate');
            el.addClass('__ezcbt_Answer');
            el.find('.__ezcbt_AnswerNum').text(i + 1);
            el.show();

            var opts = [];
            for (var j = 0; j < $('#__ezcbt_NumChoices').val(); j++) {
                opts[j] = el.find('option').clone();
            }
            el.find('option').remove();
            for (var j = 0; j < $('#__ezcbt_NumChoices').val(); j++) {
                opts[j].val(j);
                opts[j].attr('selected', (j == data.charAt(i)));
                opts[j].text(String.fromCharCode(0x41 + j));
                opts[j].appendTo(el.find('select'));
            }
            el.find('select').change(function() {
                ezcbtImportCorrectAnswers();
            });

            el.appendTo('#__ezcbt_AnswerTable');
        }
    }

    $(document).ready(function() {
        $('#__ezcbt_CalcDuration').click(function() {
            var duration = $('#__ezcbt_Duration').val();
            var hours = Math.floor(duration / 3600);
            var mins = Math.floor(duration / 60) % 60;
            var secs = Math.floor(duration) % 60;
            window.alert('Durasi: ' + hours + ' jam ' + mins + ' menit ' + secs + ' detik');
        });

        $('#__ezcbt_UpdateQuestions').click(function() {
            if (window.confirm('Apakah anda yakin ingin memperbarui data jawaban benar?')) {
                ezcbtUpdateQuestions();
                $('#__ezcbt_UpdateQuestions').attr('disabled', true);
            }
        });

        $('#__ezcbt_NumQuestions, #__ezcbt_NumChoices').change(function() {
            $('#__ezcbt_UpdateQuestions').attr('disabled', false);
        });

        ezcbtUpdateQuestions();
    });
</script>

