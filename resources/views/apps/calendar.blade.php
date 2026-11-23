@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="mb-1 h2">Calendar</h1>
                    <p class="mb-0 text-secondary">Jadwal meeting, deadline, dan agenda tim.</p>
                </div>
                <a href="#" class="btn btn-primary">New Event</a>
            </div>
        </div>
    </div>

    <div class="row g-6">
        <div class="col-xl-8 col-12">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-5">
                        <h5 class="mb-0">Oktober 2026</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-white btn-sm">Hari ini</button>
                            <button type="button" class="btn btn-white btn-icon btn-sm" aria-label="Previous">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 6l-6 6l6 6" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-white btn-icon btn-sm" aria-label="Next">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-top mb-0 calendar-grid">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-secondary small">Sen</th>
                                    <th class="text-secondary small">Sel</th>
                                    <th class="text-secondary small">Rab</th>
                                    <th class="text-secondary small">Kam</th>
                                    <th class="text-secondary small">Jum</th>
                                    <th class="text-secondary small">Sab</th>
                                    <th class="text-secondary small">Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-secondary" style="height: 110px">28</td>
                                    <td class="text-secondary" style="height: 110px">29</td>
                                    <td class="text-secondary" style="height: 110px">30</td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">1</div>
                                        <span class="badge bg-primary-subtle text-primary-emphasis d-block text-truncate">Kickoff</span>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">2</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">3</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">4</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">5</div>
                                        <span class="badge bg-info-subtle text-info-emphasis d-block text-truncate mb-1">Review</span>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">6</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">7</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">8</div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis d-block text-truncate">Deadline</span>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">9</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">10</div>
                                    </td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">11</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">12</div></td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">13</div>
                                        <span class="badge bg-success-subtle text-success-emphasis d-block text-truncate">Launch</span>
                                    </td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">14</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">15</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">16</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">17</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">18</div></td>
                                </tr>
                                <tr>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">19</div></td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">20</div>
                                        <span class="badge bg-primary-subtle text-primary-emphasis d-block text-truncate">Standup</span>
                                    </td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">21</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">22</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">23</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">24</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">25</div></td>
                                </tr>
                                <tr>
                                    <td class="bg-primary-subtle" style="height: 110px">
                                        <div class="fw-bold mb-2">26</div>
                                        <span class="badge bg-primary text-white d-block text-truncate mb-1">Sprint Review</span>
                                        <span class="badge bg-info-subtle text-info-emphasis d-block text-truncate">1:1 Meeting</span>
                                    </td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">27</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">28</div></td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">29</div></td>
                                    <td style="height: 110px">
                                        <div class="fw-semibold mb-2">30</div>
                                        <span class="badge bg-warning-subtle text-warning-emphasis d-block text-truncate">Report</span>
                                    </td>
                                    <td style="height: 110px"><div class="fw-semibold mb-2">31</div></td>
                                    <td class="text-secondary" style="height: 110px"><div class="fw-semibold mb-2">1</div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-12">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-5">Upcoming Events</h5>

                    <div class="d-flex gap-3 mb-4">
                        <div class="icon-shape rounded-2 bg-primary-subtle text-primary-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-semibold">Sprint Review</div>
                            <small class="text-secondary">Hari ini, 10:00 - 11:00</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="icon-shape rounded-2 bg-info-subtle text-info-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-semibold">1:1 Meeting</div>
                            <small class="text-secondary">Hari ini, 14:00 - 14:30</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="icon-shape rounded-2 bg-warning-subtle text-warning-emphasis">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-flag">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 5a5 5 0 0 1 7 0a5 5 0 0 0 7 0v9a5 5 0 0 1 -7 0a5 5 0 0 0 -7 0v-9z" />
                                <path d="M5 21v-7" />
                            </svg>
                        </div>
                        <div>
                            <div class="fw-semibold">Monthly Report</div>
                            <small class="text-secondary">30 Okt 2026, 09:00</small>
                        </div>
                    </div>

                    <a href="#" class="btn btn-white btn-sm mt-4 w-100">Lihat semua event</a>
                </div>
            </div>
        </div>
    </div>
@endsection
