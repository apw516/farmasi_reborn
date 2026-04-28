@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Analisa Fast Moving</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-filter me-1"></i> Filter Analisa</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Mulai Tanggal</label>
                            <input type="date" id="tgl_mulai" class="form-control"
                                value="{{ date('Y-m-01', strtotime('-3 months')) }}">
                        </div>
                        <div class="col-md-3">
                            <label>Sampai Tanggal</label>
                            <input type="date" id="tgl_selesai" class="form-control" value="{{ date('Y-m-t') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Limit Data</label>
                            <select id="limit_data" class="form-control">
                                <option value="5">Top 5</option>
                                <option value="10" selected>Top 10</option>
                                <option value="20">Top 20</option>
                                <option value="50">Top 50</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Pilih Unit</label>
                            <select id="unit_data" class="form-control">
                                @foreach ($unit as $uu)
                                    <option value="{{ $uu->kode_unit }}">{{ $uu->nama_unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="btnFilter" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Update
                                Grafik</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mt-4">
                          <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-line me-1"></i> Analisa Barang Fast Moving
                            </h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 400px;">
                                <canvas id="myBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow mb-4 mt-4">
                        <div class="card-header py-3 bg-success text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-line me-1"></i> Tren Pengeluaran Obat Mingguan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div style="height: 400px;">
                                <canvas id="weeklyLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4 mt-3">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-chart-pie me-1"></i> Kontribusi Nilai Transaksi (Rp)
                    </h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="valuePieChart"></canvas>
                    </div>
                    <hr>
                    <div id="pie-legend" class="small text-muted"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Deklarasikan variabel global untuk kedua chart
        let myBarChart;
        let myWeeklyChart;

        function loadChart() {
            spinner_on()
            let tgl_mulai = $('#tgl_mulai').val();
            let tgl_selesai = $('#tgl_selesai').val();
            let limit = $('#limit_data').val();
            let unit_data = $('#unit_data').val();
            $.ajax({
                url: "{{ route('chart.fastmoving') }}",
                method: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    tgl_mulai,
                    tgl_selesai,
                    limit,
                    unit_data
                },
                success: function(response) {
                    // 2. Destroy Bar Chart lama
                    if (myBarChart) myBarChart.destroy();
                    spinner_off()
                    var ctx = document.getElementById("myBarChart");
                    myBarChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: "Total Unit Keluar",
                                backgroundColor: "#4e73df",
                                data: response.values,
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        }

        function loadWeeklyChart() {
            spinner_on()
            $.ajax({
                url: "{{ route('chart.weekly') }}",
                method: "GET",
                data: {
                    tgl_mulai: $('#tgl_mulai').val(),
                    tgl_selesai: $('#tgl_selesai').val(),
                    limit: $('#limit_data').val(),
                    unit_data: $('#unit_data').val()
                },
                success: function(response) {
                    spinner_off()
                    var ctx = document.getElementById("weeklyLineChart").getContext("2d");

                    // 3. PENTING: Destroy Weekly Chart lama sebelum membuat yang baru
                    if (myWeeklyChart) {
                        myWeeklyChart.destroy();
                    }

                    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(40, 167, 69, 0.5)');
                    gradient.addColorStop(1, 'rgba(40, 167, 69, 0)');

                    // 4. Simpan instance baru ke variabel global
                    myWeeklyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: "Total Barang Keluar",
                                borderColor: "#28a745",
                                backgroundColor: gradient,
                                fill: true,
                                data: response.values,
                                tension: 0.3,
                                pointRadius: 5,
                                pointBackgroundColor: "#28a745"
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }
        $(document).ready(function() {
            loadChart();
            loadWeeklyChart();
            $('#btnFilter').click(function() {
                loadChart();
                loadWeeklyChart();
                loadPieChart()
            });
        });
    </script>
    <script>
        let myPieChart;

        function loadPieChart() {
            $.ajax({
                url: "{{ route('chart.value_contribution') }}",
                method: "GET",
                data: {
                    tgl_mulai: $('#tgl_mulai').val(),
                    tgl_selesai: $('#tgl_selesai').val(),
                    limit: $('#limit_data').val(),
                    unit_data: $('#unit_data').val()
                },
                success: function(response) {
                    if (myPieChart) myPieChart.destroy();
                    var ctx = document.getElementById("valuePieChart").getContext("2d");
                    myPieChart = new Chart(ctx, {
                        type: 'doughnut', // Atau 'pie'
                        data: {
                            labels: response.labels,
                            datasets: [{
                                data: response.values,
                                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                                    '#e74a3b', '#858796'
                                ],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed || 0;
                                            return label + ': Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            cutout: '70%', // Membuat lubang di tengah agar jadi doughnut chart
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            loadPieChart();
        });
    </script>
@endsection
