<?php include 'header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h2>Kategori Ziyaret İstatistikleri</h2>
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
    <!-- En Popüler Kategoriler Grafiği -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">En Çok Ziyaret Edilen Kategoriler</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadTopChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="topCategoriesChart"></canvas>
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
                <h6 class="m-0 font-weight-bold text-primary">Kategori Ziyaret Dağılımı</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadPieChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="kategoriPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ziyaret Haritası ve Kategori Ağacı -->
<div class="row">
    <!-- Kategori Ziyaret Haritası -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Kategori Ziyaret Haritası</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dışa Aktar:</div>
                        <a class="dropdown-item" href="#" id="downloadHeatmapChart">PNG İndir</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ziyaretHeatmapChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Ağacı -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kategori Ağacı ve Ziyaret Dağılımı</h6>
            </div>
            <div class="card-body">
                <div id="kategori-agaci" class="tree-container">
                    <!-- Kategori ağacı burada oluşturulacak -->
                    <div class="text-center p-3" id="tree-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Tablosu -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Kategori Ziyaret Detayları</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="kategoriTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Kategori ID</th>
                        <th>Kategori Adı</th>
                        <th>Üst Kategori</th>
                        <th>Toplam Ziyaret</th>
                        <th>Yüzde</th>
                        <th>Grafik</th>
                    </tr>
                </thead>
                <tbody id="kategori-data">
                    <!-- AJAX ile doldurulacak -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Chart.js kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Kategori ağacı stilleri */
.tree-container {
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
}

.tree-node {
    padding: 8px 0;
    border-radius: 4px;
    transition: background-color 0.2s;
    position: relative;
}

.tree-node:hover {
    background-color: rgba(0,0,0,0.03);
}

.tree-content {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.tree-children {
    padding-left: 24px;
    border-left: 1px dashed rgba(0,0,0,0.1);
    margin-left: 12px;
}

.node-icon {
    margin-right: 8px;
    width: 20px;
    text-align: center;
}

.node-toggle {
    margin-right: 5px;
    width: 20px;
    text-align: center;
    cursor: pointer;
    font-size: 12px;
}

.node-name {
    flex-grow: 1;
    font-weight: 500;
}

.node-count {
    margin-left: 8px;
    font-weight: bold;
    background-color: #4e73df;
    color: white;
    border-radius: 20px;
    padding: 2px 8px;
    font-size: 0.85em;
}

.progress-mini {
    height: 4px;
    margin-top: 4px;
    border-radius: 2px;
    overflow: hidden;
    background-color: #e9ecef;
}

.progress-mini .progress-bar {
    background-color: #4e73df;
}

/* Animasyonlar */
.fade-enter {
    opacity: 0;
}

.fade-enter-active {
    opacity: 1;
    transition: opacity 300ms;
}

.fade-exit {
    opacity: 1;
}

.fade-exit-active {
    opacity: 0;
    transition: opacity 300ms;
}

/* Tablodaki mini-grafikler için stil */
.mini-bar {
    height: 20px;
    background-color: #4e73df;
    border-radius: 3px;
    min-width: 2%;
}
</style>

<script>
$(document).ready(function() {
    // Grafik renkleri
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#6f42c1', '#fd7e14', '#20c9a6', '#5a5c69', '#858796'
    ];
    
    // Grafik nesneleri
    let topCategoriesChart, kategoriPieChart, ziyaretHeatmapChart;
    
    // Varsayılan periyot
    let currentPeriod = 'month';
    
    // Veri yükleme fonksiyonu
    function loadData(period) {
        // Yükleme göstergelerini göster
        $("#tree-loading").show();
        
        $.ajax({
            type: "GET",
            url: "../netting/islem.php",
            data: {
                islem: "kategori_ziyaret_istatistik",
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
                    updateTopCategoriesChart(response.populer_kategoriler);
                    updatePieChart(response.populer_kategoriler);
                    updateHeatmapChart(response.ziyaret_data);
                    buildCategoryTree(response.kategori_data);
                    updateKategoriTable(response.kategori_data);
                    
                    // Yükleme göstergelerini gizle
                    $("#tree-loading").hide();
                } else {
                    $("#errorAlert").text(response.mesaj);
                    $("#errorAlert").show();
                    $("#tree-loading").hide();
                    setTimeout(function() { $("#errorAlert").hide(); }, 3000);
                }
            },
            error: function(xhr, status, error) {
                $("#errorAlert").text("Veriler yüklenirken bir hata oluştu.");
                $("#errorAlert").show();
                $("#tree-loading").hide();
                setTimeout(function() { $("#errorAlert").hide(); }, 3000);
            }
        });
    }
    
    // En popüler kategoriler grafiğini güncelleme
    function updateTopCategoriesChart(data) {
        const ctx = document.getElementById('topCategoriesChart').getContext('2d');
        
        // Verileri sırala (en yüksek ziyaretten en düşüğe)
        const sortedData = [...data].sort((a, b) => b.ziyaret_sayisi - a.ziyaret_sayisi).slice(0, 10);
        
        const labels = sortedData.map(item => item.kategori_isim);
        const values = sortedData.map(item => parseInt(item.ziyaret_sayisi));
        
        // Toplam değeri hesapla (yüzdeler için)
        const total = values.reduce((sum, value) => sum + value, 0);
        
        // Eğer grafik zaten varsa yok et
        if (topCategoriesChart) {
            topCategoriesChart.destroy();
        }
        
        // Yeni grafik oluştur
        topCategoriesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ziyaret Sayısı',
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 1
                }]
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
                        grid: {
                            display: false
                        },
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
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${value} ziyaret (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Kategori pasta grafiğini güncelleme
    function updatePieChart(data) {
        const ctx = document.getElementById('kategoriPieChart').getContext('2d');
        
        // En çok ziyaret edilen 5 kategoriyi al, kalanları "Diğer" olarak birleştir
        const topCategories = [...data].sort((a, b) => b.ziyaret_sayisi - a.ziyaret_sayisi);
        
        let chartData;
        let chartLabels;
        
        if (topCategories.length > 5) {
            const top5 = topCategories.slice(0, 5);
            const others = topCategories.slice(5);
            
            const othersTotal = others.reduce((sum, item) => sum + parseInt(item.ziyaret_sayisi), 0);
            
            chartLabels = [...top5.map(item => item.kategori_isim), "Diğer Kategoriler"];
            chartData = [...top5.map(item => parseInt(item.ziyaret_sayisi)), othersTotal];
        } else {
            chartLabels = topCategories.map(item => item.kategori_isim);
            chartData = topCategories.map(item => parseInt(item.ziyaret_sayisi));
        }
        
        // Eğer grafik zaten varsa yok et
        if (kategoriPieChart) {
            kategoriPieChart.destroy();
        }
        
        // Yeni grafik oluştur
        kategoriPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
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
                                return `${label}: ${value} ziyaret (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Ziyaret ısı haritasını güncelleme
    function updateHeatmapChart(data) {
        const ctx = document.getElementById('ziyaretHeatmapChart').getContext('2d');
        
        // Verileri zaman ve kategoriye göre gruplandır
        const timeLabels = [...new Set(data.map(item => item.zaman))].sort();
        const categoryLabels = [...new Set(data.map(item => item.kategori_isim))];
        
        // En çok ziyaret edilen ilk 8 kategoriyi al
        const categoryVisits = {};
        data.forEach(item => {
            if (!categoryVisits[item.kategori_isim]) {
                categoryVisits[item.kategori_isim] = 0;
            }
            categoryVisits[item.kategori_isim] += parseInt(item.ziyaret_sayisi);
        });
        
        // Kategorileri ziyaret sayısına göre sırala ve ilk 8'i al
        const topCategories = Object.keys(categoryVisits)
            .map(cat => ({ name: cat, visits: categoryVisits[cat] }))
            .sort((a, b) => b.visits - a.visits)
            .slice(0, 8)
            .map(cat => cat.name);
        
        // Her zaman ve kategori için ziyaret sayılarını hazırla
        const datasets = topCategories.map((category, index) => {
            const categoryData = timeLabels.map(time => {
                const match = data.find(item => item.zaman === time && item.kategori_isim === category);
                return match ? parseInt(match.ziyaret_sayisi) : 0;
            });
            
            return {
                label: category,
                data: categoryData,
                backgroundColor: colors[index % colors.length],
                borderColor: colors[index % colors.length],
                borderWidth: 1.5,
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: false,
                tension: 0.1
            };
        });
        
        // Eğer grafik zaten varsa yok et
        if (ziyaretHeatmapChart) {
            ziyaretHeatmapChart.destroy();
        }
        
        // Yeni grafik oluştur
        ziyaretHeatmapChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timeLabels,
                datasets: datasets
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: "rgba(0,0,0,0.05)"
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0,0,0,0.05)"
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    }
    
    // Kategori ağacını oluştur
    function buildCategoryTree(categories) {
        const container = $("#kategori-agaci");
        container.empty();
        
        // Ziyaret sayılarının toplamını hesapla
        const totalVisits = categories.reduce((sum, cat) => sum + parseInt(cat.ziyaret_sayisi), 0);
        
        // Kategori ağacını oluştur
        function createCategoryTree(parentId = null, level = 0) {
            const children = categories.filter(cat => cat.parent_kategori_id === parentId);
            if (children.length === 0) return '';
            
            let html = '<div class="tree-children">';
            children.forEach(category => {
                const hasChildren = categories.some(cat => cat.parent_kategori_id === category.id);
                const visits = parseInt(category.ziyaret_sayisi);
                const percentage = (visits / totalVisits * 100).toFixed(1);
                
                html += `
                <div class="tree-node" data-id="${category.id}">
                    <div class="tree-content">
                        <div class="node-toggle">${hasChildren ? '<i class="fas fa-caret-right"></i>' : ''}</div>
                        <div class="node-icon"><i class="fas fa-folder"></i></div>
                        <div class="node-name">${category.kategori_isim}</div>
                        <div class="node-count">${visits}</div>
                    </div>
                    <div class="progress-mini">
                        <div class="progress-bar" style="width: ${percentage}%"></div>
                    </div>
                    ${hasChildren ? createCategoryTree(category.id, level + 1) : ''}
                </div>`;
            });
            html += '</div>';
            return html;
        }
        
        // Ana kategorileri bul
        const rootCategories = categories.filter(cat => cat.parent_kategori_id === null);
        let treeHtml = '';
        
        rootCategories.forEach(category => {
            const hasChildren = categories.some(cat => cat.parent_kategori_id === category.id);
            const visits = parseInt(category.ziyaret_sayisi);
            const percentage = (visits / totalVisits * 100).toFixed(1);
            
            treeHtml += `
            <div class="tree-node" data-id="${category.id}">
                <div class="tree-content">
                    <div class="node-toggle">${hasChildren ? '<i class="fas fa-caret-right"></i>' : ''}</div>
                    <div class="node-icon"><i class="fas fa-folder"></i></div>
                    <div class="node-name">${category.kategori_isim}</div>
                    <div class="node-count">${visits}</div>
                </div>
                <div class="progress-mini">
                    <div class="progress-bar" style="width: ${percentage}%"></div>
                </div>
                ${hasChildren ? createCategoryTree(category.id, 1) : ''}
            </div>`;
        });
        
        container.html(treeHtml);
        
        // Ağaç düğümlerine tıklama olayı ekle
        $(".node-toggle").on("click", function(e) {
            e.stopPropagation();
            const $icon = $(this).find("i");
            const $node = $(this).closest(".tree-node");
            const $children = $node.children(".tree-children");
            
            if ($icon.hasClass("fa-caret-right")) {
                $icon.removeClass("fa-caret-right").addClass("fa-caret-down");
                $children.slideDown();
            } else {
                $icon.removeClass("fa-caret-down").addClass("fa-caret-right");
                $children.slideUp();
            }
        });
        
        // Kategori adına tıklama olayı
        $(".node-name").on("click", function() {
            const $node = $(this).closest(".tree-node");
            const $toggle = $node.find(".node-toggle i");
            
            if ($toggle.length) {
                if ($toggle.hasClass("fa-caret-right")) {
                    $toggle.removeClass("fa-caret-right").addClass("fa-caret-down");
                    $node.children(".tree-children").slideDown();
                } else {
                    $toggle.removeClass("fa-caret-down").addClass("fa-caret-right");
                    $node.children(".tree-children").slideUp();
                }
            }
        });
        
        // Başlangıçta tüm alt kategorileri gizle
        $(".tree-children .tree-children").hide();
    }
    
    // Kategori tablosunu güncelle
    function updateKategoriTable(categories) {
        const container = $("#kategori-data");
        container.empty();
        
        // Toplam ziyaret sayısını hesapla
        const totalVisits = categories.reduce((sum, cat) => sum + parseInt(cat.ziyaret_sayisi), 0);
        
        // Kategorileri ziyaret sayısına göre sırala
        const sortedCategories = [...categories].sort((a, b) => b.ziyaret_sayisi - a.ziyaret_sayisi);
        
        // Tabloyu doldur
        sortedCategories.forEach(category => {
            const visits = parseInt(category.ziyaret_sayisi);
            const percentage = (visits / totalVisits * 100).toFixed(1);
            
            // Üst kategori adını bul
            let parentName = "-";
            if (category.parent_kategori_id) {
                const parentCategory = categories.find(cat => cat.id === category.parent_kategori_id);
                if (parentCategory) {
                    parentName = parentCategory.kategori_isim;
                }
            }
            
            container.append(`
                <tr>
                    <td>${category.id}</td>
                    <td>${category.kategori_isim}</td>
                    <td>${parentName}</td>
                    <td>${visits}</td>
                    <td>${percentage}%</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: ${percentage}%" 
                                 aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                                ${percentage}%
                            </div>
                        </div>
                    </td>
                </tr>
            `);
        });
    }
    
    // PNG indirme işlevleri
    $("#downloadTopChart").on("click", function() {
        if (topCategoriesChart) {
            const url = topCategoriesChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'populer-kategoriler.png';
            link.click();
        }
    });
    
    $("#downloadPieChart").on("click", function() {
        if (kategoriPieChart) {
            const url = kategoriPieChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'kategori-dagilimi.png';
            link.click();
        }
    });
    
    $("#downloadHeatmapChart").on("click", function() {
        if (ziyaretHeatmapChart) {
            const url = ziyaretHeatmapChart.toBase64Image();
            const link = document.createElement('a');
            link.href = url;
            link.download = 'ziyaret-haritasi.png';
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