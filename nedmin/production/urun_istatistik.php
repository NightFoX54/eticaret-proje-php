<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Ürün Ziyaret İstatistikleri</h2>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary period-btn" data-period="week">Son 7 Gün</button>
        <button type="button" class="btn btn-primary period-btn" data-period="month">Son 30 Gün</button>
        <button type="button" class="btn btn-outline-primary period-btn" data-period="year">Son 12 Ay</button>
    </div>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Grafikler -->
<div class="row">
    <!-- Ziyaretçi Grafiği -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ürün Ziyaret İstatistikleri</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadZiyaretChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ziyaretciChart"></canvas>
                </div>
                <div class="text-center small mt-3" id="chart-period-info">
                    <!-- Periyot bilgisi burada gösterilecek -->
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Dağılımı -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Kategorilere Göre Ziyaret Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadKategoriChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Marka İstatistikleri -->
<div class="row">
    <!-- Marka Ziyaret Dağılımı -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Markalara Göre Ziyaret Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadMarkaChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="markaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Marka Performans Özeti -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Marka Performans Özeti</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Marka</th>
                                <th>Ziyaret</th>
                                <th>Oran</th>
                            </tr>
                        </thead>
                        <tbody id="marka-ozet">
                            <!-- AJAX ile doldurulacak -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- En Çok Ziyaret Edilen Ürünler -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">En Çok Ziyaret Edilen Ürünler</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="urunTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ürün Adı</th>
                        <th>Marka</th>
                        <th>Fiyat</th>
                        <th>Toplam Ziyaret</th>
                        <th>Tekil Ziyaretçi</th>
                        <th>Dönüşüm Oranı</th>
                        <th>Aksiyon</th>
                    </tr>
                </thead>
                <tbody id="urun-data">
                    <!-- AJAX ile doldurulacak -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Popüler Ürünler Grafiği -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">En Popüler Ürünler - Ziyaret Karşılaştırması</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                aria-labelledby="dropdownMenuLink">
                <div class="dropdown-header">Dışa Aktar:</div>
                <a class="dropdown-item" href="#" id="downloadPopulerChart">PNG İndir</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-bar">
            <canvas id="populerUrunChart"></canvas>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Chart.js kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Grafik renkleri
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#6f42c1', '#fd7e14', '#20c9a6', '#5a5c69', '#858796'
    ];
    
    // Grafik nesneleri
    let ziyaretciChart, kategoriChart, populerUrunChart, markaChart;
    
    // Varsayılan periyot
    let currentPeriod = 'month';
    
    // Veri yükleme fonksiyonu
    function loadData(period) {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "urun_ziyaret_istatistik",
                period: period
            },
            dataType: "json",
            success: function(response) {
                if (response.durum === "success") {
                    // Aktif düğmeyi güncelle
                    $(".period-btn").removeClass("btn-primary").addClass("btn-outline-primary");
                    $(`.period-btn[data-period="${period}"]`).removeClass("btn-outline-primary").addClass("btn-primary");
                    
                    // Periyot bilgisini göster
                    $("#chart-period-info").text(`Gösterilen Periyot: ${response.periyot_aciklama}`);
                    
                    // Grafikleri güncelle
                    updateVisitorChart(response.ziyaret_data, period);
                    updateCategoryChart(response.kategori_data);
                    updatePopularProductChart(response.populer_urunler);
                    updateProductTable(response.populer_urunler);
                    
                    // Marka verilerini güncelle
                    updateBrandChart(response.marka_data || []);
                    updateBrandTable(response.marka_data || []);
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("Veriler yüklenirken bir hata oluştu.");
                $("#errorAlert").show();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }
    
    // Ziyaretçi grafiğini güncelleme fonksiyonu
    function updateVisitorChart(data, period) {
        const ctx = document.getElementById('ziyaretciChart').getContext('2d');
        
        const labels = data.map(item => {
            if (period === 'year') {
                // Ay-yıl formatı: 2023-01 -> Ocak 2023
                const [year, month] = item.zaman.split('-');
                const monthNames = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 
                                   'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                return `${monthNames[parseInt(month)-1]} ${year}`;
            } else {
                // Tarih formatı: 2023-01-15 -> 15.01.2023
                const date = new Date(item.zaman);
                return date.toLocaleDateString('tr-TR');
            }
        });
        
        const ziyaretData = data.map(item => parseInt(item.ziyaret_sayisi));
        const tekilData = data.map(item => parseInt(item.tekil_ziyaretci));
        
        // Eğer grafik zaten varsa yok et
        if (ziyaretciChart) {
            ziyaretciChart.destroy();
        }
        
        // Yeni grafik oluştur
        ziyaretciChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Toplam Ziyaret',
                        data: ziyaretData,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: colors[0],
                        pointBackgroundColor: colors[0],
                        pointBorderColor: colors[0],
                        pointHoverBackgroundColor: colors[0],
                        pointHoverBorderColor: colors[0],
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Tekil Ziyaretçi',
                        data: tekilData,
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: colors[1],
                        pointBackgroundColor: colors[1],
                        pointBorderColor: colors[1],
                        pointHoverBackgroundColor: colors[1],
                        pointHoverBorderColor: colors[1],
                        borderWidth: 2,
                        fill: true
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.05)"
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    // Kategori dağılımı grafiğini güncelleme
    function updateCategoryChart(data) {
        const ctx = document.getElementById('kategoriChart').getContext('2d');
        
        // Verileri hazırla
        const labels = data.map(item => item.kategori_isim);
        const values = data.map(item => parseInt(item.ziyaret_sayisi));
        
        // Eğer grafik zaten varsa yok et
        if (kategoriChart) {
            kategoriChart.destroy();
        }
        
        // Yeni grafik oluştur
        kategoriChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '50%'
            }
        });
    }
    
    // En popüler ürünler grafiğini güncelleme
    function updatePopularProductChart(data) {
        const ctx = document.getElementById('populerUrunChart').getContext('2d');
        
        // Verileri hazırla (en fazla 10 ürün)
        const products = data.slice(0, 10);
        const labels = products.map(product => product.urun_isim);
        const totalValues = products.map(product => parseInt(product.ziyaret_sayisi));
        const uniqueValues = products.map(product => parseInt(product.tekil_ziyaretci));
        
        // Eğer grafik zaten varsa yok et
        if (populerUrunChart) {
            populerUrunChart.destroy();
        }
        
        // Yeni grafik oluştur
        populerUrunChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Toplam Ziyaret',
                        data: totalValues,
                        backgroundColor: colors[0],
                        borderWidth: 1
                    },
                    {
                        label: 'Tekil Ziyaretçi',
                        data: uniqueValues,
                        backgroundColor: colors[1],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        titleMarginBottom: 10,
                        titleFont: {
                            size: 14
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }
    
    // Marka grafiğini güncelleme fonksiyonu
    function updateBrandChart(data) {
        const ctx = document.getElementById('markaChart').getContext('2d');
        
        // Markaya göre toplam ziyaretleri grupla
        let markaToplam = {};
        data.forEach(item => {
            const marka = item.marka_isim || 'Belirtilmemiş';
            if (!markaToplam[marka]) {
                markaToplam[marka] = {
                    ziyaret_sayisi: 0,
                    tekil_ziyaretci: 0
                };
            }
            markaToplam[marka].ziyaret_sayisi += parseInt(item.ziyaret_sayisi);
            markaToplam[marka].tekil_ziyaretci += parseInt(item.tekil_ziyaretci);
        });
        
        // En çok ziyaret edilen ilk 10 markayı alıyoruz
        const topMarkas = Object.entries(markaToplam)
            .map(([marka, data]) => ({
                marka: marka,
                ziyaret_sayisi: data.ziyaret_sayisi,
                tekil_ziyaretci: data.tekil_ziyaretci
            }))
            .sort((a, b) => b.ziyaret_sayisi - a.ziyaret_sayisi)
            .slice(0, 10);
        
        const labels = topMarkas.map(item => item.marka);
        const ziyaretValues = topMarkas.map(item => item.ziyaret_sayisi);
        const tekilValues = topMarkas.map(item => item.tekil_ziyaretci);
        
        // Eğer grafik zaten varsa yok et
        if (markaChart) {
            markaChart.destroy();
        }
        
        // Yeni grafik oluştur
        markaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Toplam Ziyaret',
                        data: ziyaretValues,
                        backgroundColor: colors[5], // Mor renk kullan
                        borderWidth: 1
                    },
                    {
                        label: 'Tekil Ziyaretçi',
                        data: tekilValues,
                        backgroundColor: colors[6], // Turuncu renk kullan
                        borderWidth: 1
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        titleMarginBottom: 10,
                        titleFont: {
                            size: 14
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }
    
    // Marka tablosunu güncelleme fonksiyonu
    function updateBrandTable(data) {
        const tbody = $("#marka-ozet");
        tbody.empty();
        
        // Markaya göre toplam ziyaretleri grupla
        let markaToplam = {};
        data.forEach(item => {
            const marka = item.marka_isim || 'Belirtilmemiş';
            if (!markaToplam[marka]) {
                markaToplam[marka] = 0;
            }
            markaToplam[marka] += parseInt(item.ziyaret_sayisi);
        });
        
        // Toplam ziyaret sayısını hesapla
        const toplamZiyaret = Object.values(markaToplam).reduce((sum, count) => sum + count, 0);
        
        // Markaları ziyaret sayısına göre sırala
        const sortedMarkas = Object.entries(markaToplam)
            .map(([marka, ziyaretSayisi]) => ({
                marka: marka,
                ziyaret_sayisi: ziyaretSayisi,
                oran: ((ziyaretSayisi / toplamZiyaret) * 100).toFixed(1)
            }))
            .sort((a, b) => b.ziyaret_sayisi - a.ziyaret_sayisi);
        
        // Tabloyu doldur
        sortedMarkas.forEach((item, index) => {
            // Sadece ilk 10 markayı göster, diğerlerini "Diğer" olarak grupla
            if (index < 10) {
                const row = `
                    <tr>
                        <td>${item.marka}</td>
                        <td>${item.ziyaret_sayisi}</td>
                        <td>${item.oran}%</td>
                    </tr>
                `;
                tbody.append(row);
            }
        });
    }
    
    // Ürün tablosunu güncelleme
    function updateProductTable(data) {
        const tbody = $("#urun-data");
        tbody.empty();
        
        data.forEach(product => {
            // Burada dönüşüm oranı simüle ediliyor, gerçek veride bunu satış/ziyaret olarak hesaplayabilirsiniz
            const donusumOrani = ((Math.random() * 5) + 1).toFixed(2); // Rastgele %1-6 arası
            const markaIsim = product.marka_isim || 'Belirtilmemiş';
            
            const row = `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.urun_isim}</td>
                    <td>${markaIsim}</td>
                    <td>${parseFloat(product.urun_fiyat).toFixed(2)} ₺</td>
                    <td>${product.ziyaret_sayisi}</td>
                    <td>${product.tekil_ziyaretci}</td>
                    <td>%${donusumOrani}</td>
                    <td>
                        <a href="urun.php?urun_id=${product.id}" class="btn btn-sm btn-info">
                            <i class="fas fa-search"></i> Detay
                        </a>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // PNG indirme işlevleri
    $("#downloadZiyaretChart").on("click", function() {
        if (ziyaretciChart) {
            const url = ziyaretciChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'urun-ziyaret-grafigi.png';
            link.click();
        }
    });
    
    $("#downloadKategoriChart").on("click", function() {
        if (kategoriChart) {
            const url = kategoriChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'kategori-dagilimi.png';
            link.click();
        }
    });
    
    $("#downloadPopulerChart").on("click", function() {
        if (populerUrunChart) {
            const url = populerUrunChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'populer-urunler.png';
            link.click();
        }
    });
    
    $("#downloadMarkaChart").on("click", function() {
        if (markaChart) {
            const url = markaChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'marka-dagilimi.png';
            link.click();
        }
    });
    
    // Periyot düğmelerine tıklama olayı
    $(".period-btn").on("click", function() {
        const period = $(this).data("period");
        currentPeriod = period;
        loadData(period);
    });
    
    // Sayfa yüklendiğinde varsayılan verileri yükle
    loadData(currentPeriod);
});
</script> 