<?php include 'header.php'; ?>

<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Ürün Değerlendirmeleri</h3>
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
                                    <th>Puan</th>
                                    <th>Yorum</th>
                                    <th>Tarih</th>
                                    <th>Mağaza Yanıtı</th>
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

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Değerlendirmeye Yanıt Ver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="review_id">
                <div class="form-group">
                    <label>Yanıtınız:</label>
                    <textarea class="form-control" id="reply_text" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="submitReply">Yanıtı Gönder</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#datatable').DataTable({
        "ajax": {
            "url": "../netting/islem.php?islem=get_all_reviews",
            "type": "GET",
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "urun_isim" },
            { "data": "musteri_ad" },
            { 
                "data": "puan",
                "render": function(data) {
                    return '⭐'.repeat(data);
                }
            },
            { "data": "yorum" },
            { 
                "data": "tarih",
                "render": function(data) {
                    return new Date(data).toLocaleString('tr-TR');
                }
            },
            { 
                "data": "magaza_yanit",
                "render": function(data, type, row) {
                    return data ? data : '<span class="text-muted">Yanıt verilmemiş</span>';
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    var replyBtn = !row.magaza_yanit ? 
                        `<button class="btn btn-primary btn-sm reply-btn" data-id="${row.id}">Yanıtla</button>` : 
                        `<button class="btn btn-info btn-sm reply-btn" data-id="${row.id}">Yanıtı Düzenle</button>`;
                    return replyBtn;
                }
            }
        ],
        "order": [[5, "desc"]]
    });

    // Handle reply button click
    $(document).on('click', '.reply-btn', function() {
        var reviewId = $(this).data('id');
        $('#review_id').val(reviewId);
        
        // Get existing reply if any
        var row = table.row($(this).closest('tr')).data();
        $('#reply_text').val(row.magaza_yanit || '');
        
        $('#replyModal').modal('show');
    });

    // Handle reply submission
    $('#submitReply').click(function() {
        var reviewId = $('#review_id').val();
        var replyText = $('#reply_text').val();

        if (!replyText.trim()) {
            $('#error-alert').text('Lütfen bir yanıt yazın.').show();
            setTimeout(function() { $('#error-alert').hide(); }, 3000);
            return;
        }

        $.ajax({
            url: '../netting/islem.php',
            type: 'POST',
            data: {
                islem: 'reply_to_review',
                review_id: reviewId,
                reply: replyText
            },
            success: function(response) {
                var result = JSON.parse(response);
                if (result.durum === 'success') {
                    $('#replyModal').modal('hide');
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