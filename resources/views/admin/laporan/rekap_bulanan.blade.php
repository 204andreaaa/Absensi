@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h1>{{ $pageTitle }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-2 mb-md-0">{{ $cardTitle }} ({{ $bulan_label }})</h4>
            <div class="card-header-action">
                <a href="{{ route($exportPdfRoute, request()->query()) }}" target="_blank" class="btn btn-danger font-weight-bold">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak / Print PDF
                </a>
            </div>
        </div>

        <!-- FILTER FORM BULAN & DEPARTEMEN -->
        <div class="card-body bg-light border-bottom py-3">
            <form method="GET" action="{{ url()->current() }}" class="form-inline justify-content-between">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- Filter Bulan -->
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-bold text-dark">Bulan:</label>
                        <select name="bulan" class="form-control form-control-sm">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-bold text-dark">Tahun:</label>
                        <input type="number" name="tahun" class="form-control form-control-sm" value="{{ $tahun }}" style="width: 90px;">
                    </div>

                    <!-- Filter Departemen -->
                    <div class="form-group mr-3 mb-2">
                        <label class="mr-2 font-weight-bold text-dark">Departemen:</label>
                        <select name="departemen_id" class="form-control form-control-sm">
                            <option value="">-- Semua Departemen --</option>
                            @foreach($listDepartemen as $dept)
                                <option value="{{ $dept->id }}" {{ $departemenId == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm mb-2 font-weight-bold px-3">
                        <i class="fas fa-filter mr-1"></i> Tampilkan
                    </button>
                    @if(request()->hasAny(['bulan', 'tahun', 'departemen_id']))
                        <a href="{{ url()->current() }}" class="btn btn-secondary btn-sm mb-2 ml-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- MATRIX GRID TABLE -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center" style="font-size: 0.85rem; border-collapse: separate; border-spacing: 0;">
                    <thead class="bg-white">
                        <tr>
                            <th style="min-width: 40px; vertical-align: middle;" class="bg-white">NO</th>
                            <th style="min-width: 90px; vertical-align: middle;" class="bg-white">NIK</th>
                            <th style="min-width: 170px; text-align: left; vertical-align: middle;" class="bg-white">NAMA PEGAWAI</th>
                            <th style="min-width: 120px; text-align: left; vertical-align: middle;" class="bg-white">DEPARTEMEN</th>
                            
                            <!-- TANGGAL 1 - 31 -->
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php $dayHead = $daysHeader[$d]; @endphp
                                <th style="width: 32px; min-width: 32px; padding: 6px 2px; vertical-align: middle;" class="{{ $dayHead['is_off'] ? 'bg-danger text-white' : 'bg-light text-dark' }}" title="{{ $dayHead['holiday_name'] ?? ($dayHead['is_weekend'] ? 'Akhir Pekan' : '') }}">
                                    <div>{{ $d }}</div>
                                </th>
                            @endfor

                            <th style="min-width: 80px; vertical-align: middle;" class="bg-primary text-white font-weight-bold">HADIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $index => $row)
                            <tr>
                                <td class="font-weight-bold align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle text-muted">{{ $row->pegawai->nik }}</td>
                                <td class="align-middle text-left font-weight-bold text-dark">{{ $row->pegawai->nama }}</td>
                                <td class="align-middle text-left">{{ optional($row->pegawai->departemen)->nama_departemen ?? '-' }}</td>

                                <!-- STATUS ABSEN TANGGAL 1 - 31 -->
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php 
                                        $cell = $row->days[$d]; 
                                        $dayHead = $daysHeader[$d];
                                    @endphp

                                    <td class="align-middle p-1 {{ $dayHead['is_off'] ? 'bg-light' : '' }}">
                                        @if($cell['status'] === 'hadir')
                                            <span class="text-success font-weight-bold" style="font-size: 1.15rem;" title="Hadir Absen">✓</span>
                                        @elseif($cell['status'] === 'alpa')
                                            <span class="text-danger font-weight-bold" style="font-size: 1.15rem;" title="Alpa / Tidak Absen">✕</span>
                                        @else
                                            <span class="text-muted" style="opacity: 0.4;">-</span>
                                        @endif
                                    </td>
                                @endfor

                                <td class="align-middle font-weight-bold bg-light">
                                    <span class="badge badge-success px-2 py-1" style="font-size: 0.85rem;">{{ $row->total_hadir }} H</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 5 }}" class="text-center text-muted py-5">
                                    Tidak ada data pegawai atau rekap kehadiran pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KETERANGAN SYMBOL LEGEND -->
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex align-items-center flex-wrap gap-4 text-muted small">
                <span class="font-weight-bold text-dark mr-2">Keterangan Simbol:</span>
                <span class="mr-3"><strong class="text-success" style="font-size: 1.1rem;">✓</strong> = Hadir / Absen</span>
                <span class="mr-3"><strong class="text-danger" style="font-size: 1.1rem;">✕</strong> = Tidak Hadir / Alpa</span>
                <span class="mr-3"><span class="badge badge-danger px-2">Tanggal Red</span> = Libur / Akhir Pekan</span>
            </div>
        </div>

    </div>
@endsection
