@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Analytics</h1>
                    <p class="mb-0 text-secondary">Trafik pengunjung dan performa konten.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-white">7 Hari</button>
                    <button type="button" class="btn btn-primary">Download</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan angka --}}
    <div class="row row-cols-2 row-cols-xl-4 mb-6 g-6">
        <div class="col">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-secondary mb-3">
                        <span class="icon-shape icon-sm rounded-circle bg-primary-subtle text-primary-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </span>
                        <span>Visitors</span>
                    </div>
                    <div class="fs-3 fw-bold lh-1">24,521</div>
                    <div class="text-success small mt-2">+12.4% dari minggu lalu</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-secondary mb-3">
                        <span class="icon-shape icon-sm rounded-circle bg-info-subtle text-info-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6s-6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6s6.6 2 9 6" />
                            </svg>
                        </span>
                        <span>Page Views</span>
                    </div>
                    <div class="fs-3 fw-bold lh-1">88,120</div>
                    <div class="text-success small mt-2">+8.1% dari minggu lalu</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-secondary mb-3">
                        <span class="icon-shape icon-sm rounded-circle bg-warning-subtle text-warning-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M12 7v5l3 3" />
                            </svg>
                        </span>
                        <span>Avg. Session</span>
                    </div>
                    <div class="fs-3 fw-bold lh-1">3m 42s</div>
                    <div class="text-danger small mt-2">-1.2% dari minggu lalu</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-secondary mb-3">
                        <span class="icon-shape icon-sm rounded-circle bg-danger-subtle text-danger-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-back-up">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 14l-4 -4l4 -4" />
                                <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                            </svg>
                        </span>
                        <span>Bounce Rate</span>
                    </div>
                    <div class="fs-3 fw-bold lh-1">38.2%</div>
                    <div class="text-success small mt-2">-2.4% dari minggu lalu</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h5 class="mb-0">Visitors Overview</h5>
                        <span class="badge bg-success-subtle text-success-emphasis">Live</span>
                    </div>
                    <div id="visitorsChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-5">Traffic Sources</h5>
                    <div id="trafficChart" class="d-flex justify-content-center"></div>
                    <table class="table table-sm table-borderless mb-0 mt-4">
                        <tbody>
                            <tr>
                                <td>Organic Search</td>
                                <td class="text-end fw-semibold">45%</td>
                            </tr>
                            <tr>
                                <td>Direct</td>
                                <td class="text-end fw-semibold">30%</td>
                            </tr>
                            <tr>
                                <td>Social Media</td>
                                <td class="text-end fw-semibold">15%</td>
                            </tr>
                            <tr>
                                <td>Referral</td>
                                <td class="text-end fw-semibold">10%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body">
            <h5 class="mb-5">Top Pages</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th class="text-end">Views</th>
                            <th class="text-end">Unique</th>
                            <th class="text-end">Bounce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>/dashboard</td>
                            <td class="text-end">12,480</td>
                            <td class="text-end">8,210</td>
                            <td class="text-end">32%</td>
                        </tr>
                        <tr>
                            <td>/products</td>
                            <td class="text-end">9,312</td>
                            <td class="text-end">6,104</td>
                            <td class="text-end">41%</td>
                        </tr>
                        <tr>
                            <td>/pricing</td>
                            <td class="text-end">7,845</td>
                            <td class="text-end">5,320</td>
                            <td class="text-end">28%</td>
                        </tr>
                        <tr>
                            <td>/blog</td>
                            <td class="text-end">6,021</td>
                            <td class="text-end">4,118</td>
                            <td class="text-end">52%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const palette = window.themePalette();

            const visitorsChart = new ApexCharts(document.querySelector('#visitorsChart'), {
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Public Sans, sans-serif'
                },
                series: [
                    { name: 'Visitors', data: [3200, 4100, 3800, 5200, 4700, 6100, 5400] },
                    { name: 'Page Views', data: [8200, 9100, 7800, 11200, 10400, 13100, 11800] }
                ],
                colors: ['#5d87ff', '#49beff'],
                plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    labels: { style: { colors: palette.labelColor } }
                },
                yaxis: { labels: { style: { colors: palette.labelColor } } },
                grid: { borderColor: palette.gridColor },
                tooltip: { theme: palette.tooltipTheme },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: palette.labelColor }
                }
            });
            visitorsChart.render();

            const trafficChart = new ApexCharts(document.querySelector('#trafficChart'), {
                chart: { type: 'donut', height: 220, fontFamily: 'Public Sans, sans-serif' },
                series: [45, 30, 15, 10],
                labels: ['Organic', 'Direct', 'Social', 'Referral'],
                colors: ['#5d87ff', '#49beff', '#13deb9', '#ffae1f'],
                legend: { show: false },
                tooltip: { theme: palette.tooltipTheme },
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: '72%' } } }
            });
            trafficChart.render();

            // Perbarui warna chart ketika tema diganti tanpa reload.
            window.addEventListener('themechange', function () {
                const palette = window.themePalette();

                visitorsChart.updateOptions({
                    xaxis: { labels: { style: { colors: palette.labelColor } } },
                    yaxis: { labels: { style: { colors: palette.labelColor } } },
                    grid: { borderColor: palette.gridColor },
                    tooltip: { theme: palette.tooltipTheme },
                    legend: { labels: { colors: palette.labelColor } }
                });

                trafficChart.updateOptions({
                    tooltip: { theme: palette.tooltipTheme }
                });
            });
        });
    </script>
@endpush
