<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Site Ziyaret İstatistikleri</h2>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary period-btn" data-period="day">Bugün</button>
        <button type="button" class="btn btn-primary period-btn" data-period="week">Son 7 Gün</button>
        <button type="button" class="btn btn-outline-primary period-btn" data-period="month">Son 30 Gün</button>
        <button type="button" class="btn btn-outline-primary period-btn" data-period="year">Son 12 Ay</button>
    </div>
</div>

<!-- Uyarı Mesajları -->
<div class="alert alert-success" id="successAlert" style="display:none;" role="alert"></div>
<div class="alert alert-danger" id="errorAlert" style="display:none;" role="alert"></div>

<!-- Özet Kartları -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Toplam Ziyaret</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="toplam_ziyaret">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tekil Ziyaretçi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="tekil_ziyaretci">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Ziyaret Edilmiş Gün</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ziyaret_gun">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Ortalama Günlük Ziyaret</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ortalama_ziyaret">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafikler -->
<div class="row">
    <!-- Ziyaretçi Grafiği -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ziyaretçi İstatistikleri</h6>
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

    <!-- Tarayıcı Dağılımı -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tarayıcı Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadTarayiciChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="tarayiciChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- İşletim Sistemi Dağılımı -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">İşletim Sistemi Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadOsChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="osChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cihaz Dağılımı -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Cihaz Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadCihazChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="cihazChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boş Kart (Gelecekteki ek özellikler için) -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ziyaretçi Özeti</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <i class="fas fa-chart-bar fa-4x mb-3 text-gray-300"></i>
                    <p id="ziyaretci_ozet" class="mb-0">
                        <!-- Ziyaretçi özeti burada gösterilecek -->
                    </p>
                </div>
            </div>
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
    let ziyaretciChart, tarayiciChart, osChart, cihazChart;
    
    // Varsayılan periyot
    let currentPeriod = 'week';
    
    // Veri yükleme fonksiyonu
    function loadData(period) {
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "site_ziyaret_istatistik",
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
                    
                    // Özet kartlarını güncelle
                    updateSummaryCards(response.ozet);
                    
                    // Ziyaretçi grafiğini güncelle
                    updateVisitorChart(response.ziyaret_data, period);
                    
                    // Pasta grafiklerini güncelle
                    updatePieCharts(response.tarayici_data, response.os_data, response.cihaz_data);
                    
                    // Özet metni güncelle
                    updateSummaryText(response.ozet, response.periyot_aciklama);
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
    
    // Özet kartlarını güncelleme fonksiyonu
    function updateSummaryCards(ozet) {
        $("#toplam_ziyaret").text(ozet.toplam_ziyaret);
        $("#tekil_ziyaretci").text(ozet.tekil_ziyaretci);
        $("#ziyaret_gun").text(ozet.ziyaret_gun);
        
        // Ortalama günlük ziyaret hesapla
        const ortalama = Math.round(ozet.toplam_ziyaret / ozet.ziyaret_gun);
        $("#ortalama_ziyaret").text(ortalama);
    }
    
    // Ziyaretçi grafiğini güncelleme fonksiyonu
    function updateVisitorChart(data, period) {
        const ctx = document.getElementById('ziyaretciChart').getContext('2d');
        
        const labels = data.map(item => {
            if (period === 'day') {
                return item.zaman; // Saat bazında
            } else if (period === 'year') {
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
    
    // Pasta grafiklerini güncelleme fonksiyonu
    function updatePieCharts(tarayiciData, osData, cihazData) {
        // Tarayıcı pasta grafiği
        const tarayiciCtx = document.getElementById('tarayiciChart').getContext('2d');
        const tarayiciLabels = tarayiciData.map(item => item.ziyaret_tarayici || 'Bilinmiyor');
        const tarayiciValues = tarayiciData.map(item => parseInt(item.toplam));
        
        if (tarayiciChart) {
            tarayiciChart.destroy();
        }
        
        tarayiciChart = new Chart(tarayiciCtx, {
            type: 'doughnut',
            data: {
                labels: tarayiciLabels,
                datasets: [{
                    data: tarayiciValues,
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
        
        // İşletim sistemi pasta grafiği
        const osCtx = document.getElementById('osChart').getContext('2d');
        const osLabels = osData.map(item => item.ziyaret_os || 'Bilinmiyor');
        const osValues = osData.map(item => parseInt(item.toplam));
        
        if (osChart) {
            osChart.destroy();
        }
        
        osChart = new Chart(osCtx, {
            type: 'doughnut',
            data: {
                labels: osLabels,
                datasets: [{
                    data: osValues,
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
        
        // Cihaz pasta grafiği
        const cihazCtx = document.getElementById('cihazChart').getContext('2d');
        const cihazLabels = cihazData.map(item => item.ziyaret_cihaz || 'Bilinmiyor');
        const cihazValues = cihazData.map(item => parseInt(item.toplam));
        
        if (cihazChart) {
            cihazChart.destroy();
        }
        
        cihazChart = new Chart(cihazCtx, {
            type: 'doughnut',
            data: {
                labels: cihazLabels,
                datasets: [{
                    data: cihazValues,
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
    
    // Özet metni güncelleme fonksiyonu
    function updateSummaryText(ozet, periyotAciklama) {
        const toplamZiyaret = parseInt(ozet.toplam_ziyaret);
        const tekilZiyaretci = parseInt(ozet.tekil_ziyaretci);
        const ziyaretGun = parseInt(ozet.ziyaret_gun);
        
        let text = `Siteniz toplam <strong>${toplamZiyaret}</strong> ziyaret almış ve <strong>${tekilZiyaretci}</strong> farklı kullanıcı tarafından görüntülenmiştir. `;
        text += `Günlük ortalama <strong>${Math.round(toplamZiyaret / ziyaretGun)}</strong> ziyaret almaktasınız.`;
        
        $("#ziyaretci_ozet").html(text);
    }
    
    // PNG indirme işlevleri
    $("#downloadZiyaretChart").on("click", function() {
        if (ziyaretciChart) {
            const url = ziyaretciChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'ziyaretci-grafigi.png';
            link.click();
        }
    });
    
    $("#downloadTarayiciChart").on("click", function() {
        if (tarayiciChart) {
            const url = tarayiciChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'tarayici-dagilimi.png';
            link.click();
        }
    });
    
    $("#downloadOsChart").on("click", function() {
        if (osChart) {
            const url = osChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'isletim-sistemi-dagilimi.png';
            link.click();
        }
    });
    
    $("#downloadCihazChart").on("click", function() {
        if (cihazChart) {
            const url = cihazChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'cihaz-dagilimi.png';
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