<?php include 'header.php'; ?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Ürün Soruları</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <!-- Alert Messages -->
        <div id="alert-container">
            <div class="alert alert-success" style="display:none;" id="success-alert"></div>
            <div class="alert alert-danger" style="display:none;" id="error-alert"></div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ürün</th>
                                    <th>Müşteri</th>
                                    <th>Soru</th>
                                    <th>Tarih</th>
                                    <th>Cevap</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answer Modal -->
<div class="modal fade" id="answerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Soruyu Yanıtla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="question_id">
                <div class="form-group">
                    <label>Soru:</label>
                    <p id="question_text" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Yanıtınız:</label>
                    <textarea class="form-control" id="answer_text" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="make_public">
                        <label class="custom-control-label" for="make_public">Herkese Açık Yap</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="submitAnswer">Yanıtı Gönder</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#datatable').DataTable({
        "ajax": {
            "url": "../netting/islem.php?islem=get_all_questions",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "urun_isim" },
            { "data": "musteri_ad" },
            { "data": "soru" },
            { 
                "data": "tarih",
                "render": function(data) {
                    return new Date(data).toLocaleString('tr-TR');
                }
            },
            { 
                "data": "cevap",
                "render": function(data, type, row) {
                    return data ? data : '<span class="text-muted">Yanıt verilmemiş</span>';
                }
            },
            {
                "data": "herkese_acik",
                "render": function(data) {
                    return data == 1 ? 
                        '<span class="badge badge-success" style="background-color: #007bff;">Herkese Açık</span>' : 
                        '<span class="badge badge-secondary" style="background-color: #6c757d;">Gizli</span>';
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    var answerBtn = !row.cevap ? 
                        `<button class="btn btn-primary btn-sm answer-btn" data-id="${row.id}">Yanıtla</button>` : 
                        `<button class="btn btn-info btn-sm answer-btn" data-id="${row.id}">Yanıtı Düzenle</button>`;
                    return answerBtn;
                }
            }
        ],
        "order": [[4, "desc"]]
    });

    // Handle answer button click
    $(document).on('click', '.answer-btn', function() {
        var questionId = $(this).data('id');
        $('#question_id').val(questionId);
        
        // Get existing data
        var row = table.row($(this).closest('tr')).data();
        $('#question_text').text(row.soru);
        $('#answer_text').val(row.cevap || '');
        $('#make_public').prop('checked', row.herkese_acik == 1);
        
        $('#answerModal').modal('show');
    });

    // Handle answer submission
    $('#submitAnswer').click(function() {
        var questionId = $('#question_id').val();
        var answerText = $('#answer_text').val();
        var isPublic = $('#make_public').is(':checked') ? 1 : 0;

        if (!answerText.trim()) {
            $('#error-alert').text('Lütfen bir yanıt yazın.').show();
            setTimeout(function() { $('#error-alert').hide(); }, 3000);
            return;
        }

        $.ajax({
            url: '../netting/islem.php',
            type: 'POST',
            data: {
                islem: 'answer_question',
                question_id: questionId,
                answer: answerText,
                is_public: isPublic
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.durum === 'success') {
                    $('#answerModal').modal('hide');
                    $('#success-alert').text('Yanıtınız başarıyla kaydedildi.').show();
                    setTimeout(function() { $('#success-alert').hide(); }, 3000);
                    table.ajax.reload();
                } else {
                    $('#error-alert').text(result.mesaj || 'Bir hata oluştu.').show();
                    setTimeout(function() { $('#error-alert').hide(); }, 3000);
                }
            },
            error: function() {
                $('#error-alert').text('Bir hata oluştu.').show();
                setTimeout(function() { $('#error-alert').hide(); }, 3000);
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?> 