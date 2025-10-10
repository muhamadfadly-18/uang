@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold">Selamat datang, {{ Auth::user()->name }}!</h3>

    {{-- Filter --}}
    <form method="GET" class="mt-3 mb-4">
        <div class="row g-2 flex-wrap">
            <div class="col-12 col-md-3">
                <input type="date" name="tanggal" value="{{ $filterTanggal }}" class="form-control">
            </div>
            <div class="col-6 col-md-2">
                <select name="bulan" class="form-select">
                    @foreach(range(1, 12) as $b)
                        <option value="{{ $b }}" {{ $filterBulan == $b ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $b)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <input type="number" name="tahun" value="{{ $filterTahun }}" class="form-control">
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filter</button>
            </div>
            <div class="col-6 col-md-2">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100"><i class="bi bi-arrow-repeat"></i> Reset</a>
            </div>
        </div>
    </form>

    {{-- Card Statistik --}}
    <div class="row mt-3 g-3">
        <div class="col-12 col-md-4">
            <div class="card text-white bg-primary shadow-sm border-0 h-100">
                <div class="card-header"><i class="bi bi-wallet2"></i> Saldo</div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Rp {{ number_format($saldo,0,",",".") }}</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-white bg-success shadow-sm border-0 h-100">
                <div class="card-header"><i class="bi bi-graph-up-arrow"></i> Total Pemasukan</div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Rp {{ number_format($totalPemasukan,0,",",".") }}</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card text-white bg-danger shadow-sm border-0 h-100">
                <div class="card-header"><i class="bi bi-cash-stack"></i> Total Pengeluaran</div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">Rp {{ number_format($totalPengeluaran,0,",",".") }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Bar --}}
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-bar-chart-line"></i> Grafik Pemasukan & Pengeluaran (Per Hari)
        </div>
        <div class="card-body">
            <div id="chart-bar"></div>
        </div>
    </div>

    {{-- Grafik Pie --}}
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-pie-chart"></i> Perbandingan Total Bulan Ini
        </div>
        <div class="card-body">
            <div id="chart-pie"></div>
        </div>
    </div>
</div>

{{-- ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // === BAR CHART ===
    var barOptions = {
        chart: {
            type: 'bar',
            height: 380,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        series: [
            { name: 'Pemasukan', data: @json($pemasukanData) },
            { name: 'Pengeluaran', data: @json($pengeluaranData) }
        ],
        xaxis: {
            categories: @json($days->map(fn($d) => \Carbon\Carbon::parse($d)->format('d'))),
            title: { text: 'Tanggal' },
            labels: { style: { fontSize: '12px' } }
        },
        yaxis: {
            title: { text: 'Jumlah (Rp)' },
            labels: { formatter: val => val.toLocaleString('id-ID') }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '40%',
                endingShape: 'rounded',
                borderRadius: 5
            }
        },
        colors: ['#28a745', '#dc3545'],
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        legend: { position: 'top', horizontalAlign: 'center' },
        tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
        grid: { borderColor: '#f1f1f1' },
        responsive: [
            {
                breakpoint: 768,
                options: {
                    chart: { height: 300 },
                    plotOptions: { bar: { columnWidth: '60%' } },
                    legend: { fontSize: '12px' }
                }
            }
        ]
    };
    var barChart = new ApexCharts(document.querySelector("#chart-bar"), barOptions);
    barChart.render();

    // === PIE CHART ===
    var pieOptions = {
        series: [
            {{ $pieData['pemasukan'] }},
            {{ $pieData['pengeluaran'] }},
            {{ $pieData['saldo'] }}
        ],
        chart: {
            type: 'polarArea',
            height: 380,
            toolbar: { show: false }
        },
        labels: ['Pemasukan', 'Pengeluaran', 'Saldo'],
        colors: ['#28a745', '#dc3545', '#007bff'],
        stroke: { colors: ['#fff'] },
        fill: { opacity: 0.9 },
        legend: {
            position: 'bottom',
            fontSize: '14px',
            markers: { width: 16, height: 16, radius: 6 }
        },
        tooltip: { y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') } },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                const total = opts.w.globals.seriesTotals.reduce((a,b)=>a+b,0);
                const percent = (opts.w.globals.series[opts.seriesIndex]/total*100).toFixed(1);
                return percent + "%";
            }
        },
        responsive: [
            {
                breakpoint: 480,
                options: { chart: { height: 300 }, legend: { position: 'bottom' } }
            }
        ]
    };
    var pieChart = new ApexCharts(document.querySelector("#chart-pie"), pieOptions);
    pieChart.render();
</script>
@endsection
