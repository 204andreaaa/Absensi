# LAMPIRAN LISTING PROGRAM UTAMA (CONTROLLER, MODEL, & ROUTES)
## SISTEM INFORMASI PRESENSI PEGAWAI BERBASIS FACE RECOGNITION DAN LIVENESS DETECTION

---

### BAB I: CONTROLLER (LOGIKA BISNIS SISTEM)

#### 1. Controller Authentication (`app/Http/Controllers/AuthController.php`)
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = auth()->user();

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('pegawai.dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
```

#### 2. Controller Presensi Pegawai (`app/Http/Controllers/Pegawai/AbsensiPegawaiController.php`)
```php
<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Pegawai;
use Carbon\Carbon;

class AbsensiPegawaiController extends Controller
{
    private function getHolidayMessage(Carbon $date): ?string
    {
        $hariLibur = HariLibur::whereDate('tanggal', $date->toDateString())->first();
        return $hariLibur ? 'Hari ini libur: ' . $hariLibur->nama_libur : null;
    }

    private function saveAttendancePhoto(?string $imageData, int $pegawaiId, string $mode): ?string
    {
        if (!$imageData || !str_starts_with($imageData, 'data:image/')) {
            return null;
        }

        [$meta, $content] = explode(',', $imageData, 2);
        $extension = str_contains($meta, 'image/png') ? 'png' : 'jpg';
        $binary = base64_decode($content);

        if ($binary === false) {
            return null;
        }

        $filename = sprintf(
            'attendance/%s/%s_%s_%s.%s',
            Carbon::today()->format('Y-m-d'),
            $pegawaiId,
            $mode,
            now()->format('His'),
            $extension
        );

        Storage::disk('public')->put($filename, $binary);
        return $filename;
    }

    public function dashboard()
    {
        $pegawaiId = Auth::id();
        $today = Carbon::today();
        $holidayMessage = $this->getHolidayMessage($today);
        $todayString = $today->toDateString();
        $startOfMonth = $today->copy()->startOfMonth()->toDateString();

        $todayAttendance = Absensi::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $todayString)
            ->first();

        $monthlyAttendances = Absensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$startOfMonth, $todayString])
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        $stats = [
            'hadir_bulan_ini' => Absensi::where('pegawai_id', $pegawaiId)
                ->whereBetween('tanggal', [$startOfMonth, $todayString])
                ->count(),
            'sudah_masuk_hari_ini' => (bool) optional($todayAttendance)->jam_masuk,
            'sudah_pulang_hari_ini' => (bool) optional($todayAttendance)->jam_pulang,
            'dataset_count' => Auth::user()->dataset_wajahs()->count()
        ];

        return view('pegawai.dashboard', [
            'todayAttendance' => $todayAttendance,
            'monthlyAttendances' => $monthlyAttendances,
            'stats' => $stats,
            'holidayMessage' => $holidayMessage
        ]);
    }

    public function index()
    {
        $pegawai = Auth::user()->load('jadwal');
        $holidayMessage = $this->getHolidayMessage(Carbon::today());

        return view('pegawai.absensi', [
            'datasetCount' => $pegawai->dataset_wajahs()->count(),
            'minDataset' => 15,
            'jadwalPegawai' => $pegawai->jadwal,
            'holidayMessage' => $holidayMessage
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'mode' => 'required|in:masuk,pulang',
            'alasan_telat' => 'nullable|string|max:500',
            'alasan_pulang_awal' => 'nullable|string|max:500',
        ]);

        $pegawai = Auth::user();
        $today = Carbon::today();
        $todayStr = $today->toDateString();
        $mode = $request->mode;

        if ($this->getHolidayMessage($today)) {
            return response()->json(['success' => false, 'message' => 'Hari ini libur nasional.'], 422);
        }

        $photoPath = $this->saveAttendancePhoto($request->image, $pegawai->id, $mode);
        if (!$photoPath) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan foto bukti presensi.'], 422);
        }

        $absensi = Absensi::firstOrNew(['pegawai_id' => $pegawai->id, 'tanggal' => $todayStr]);

        if ($mode === 'masuk') {
            if ($absensi->jam_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi masuk hari ini.'], 422);
            }

            $jamMasuk = now()->toTimeString();
            $jadwal = $pegawai->jadwal;

            $status = 'tepat_waktu';
            if ($jadwal) {
                $batasToleransi = Carbon::parse($jadwal->jam_masuk)->addMinutes($jadwal->toleransi_telat ?? 0);
                if (now()->greaterThan($batasToleransi)) {
                    $status = 'terlambat';
                }
            }

            $absensi->jam_masuk = $jamMasuk;
            $absensi->status = $status;
            $absensi->foto_masuk = $photoPath;
            if ($status === 'terlambat' && $request->filled('alasan_telat')) {
                $absensi->alasan_telat = $request->alasan_telat;
            }
            $absensi->save();

            return response()->json(['success' => true, 'message' => 'Presensi masuk berhasil dicatat!']);
        }

        if ($mode === 'pulang') {
            if (!$absensi->jam_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda belum melakukan presensi masuk.'], 422);
            }

            if ($absensi->jam_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi pulang hari ini.'], 422);
            }

            $absensi->jam_pulang = now()->toTimeString();
            $absensi->foto_pulang = $photoPath;
            if ($request->filled('alasan_pulang_awal')) {
                $absensi->alasan_pulang_awal = $request->alasan_pulang_awal;
            }
            $absensi->save();

            return response()->json(['success' => true, 'message' => 'Presensi pulang berhasil dicatat!']);
        }

        return response()->json(['success' => false, 'message' => 'Mode presensi tidak valid.'], 422);
    }

    public function riwayat()
    {
        $pegawaiId = Auth::id();
        $riwayatAbsensi = Absensi::where('pegawai_id', $pegawaiId)
            ->orderByDesc('tanggal')
            ->paginate(10);

        $stats = [
            'total_hadir' => Absensi::where('pegawai_id', $pegawaiId)->count(),
            'tepat_waktu' => Absensi::where('pegawai_id', $pegawaiId)->where('status', 'tepat_waktu')->count(),
            'terlambat' => Absensi::where('pegawai_id', $pegawaiId)->where('status', 'terlambat')->count(),
            'pulang_cepat' => Absensi::where('pegawai_id', $pegawaiId)->whereNotNull('alasan_pulang_awal')->count(),
        ];

        return view('pegawai.riwayat', [
            'riwayatAbsensi' => $riwayatAbsensi,
            'stats' => $stats
        ]);
    }
}
```

#### 3. Controller Perekaman Dataset Wajah Pegawai (`app/Http/Controllers/Pegawai/DatasetPegawaiController.php`)
```php
<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatasetWajah;
use Illuminate\Support\Facades\Auth;

class DatasetPegawaiController extends Controller
{
    private const MIN_DATASET = 15;

    public function index()
    {
        $pegawaiId = Auth::id();
        $datasetCount = DatasetWajah::where('pegawai_id', $pegawaiId)->count();

        return view('pegawai.dataset', [
            'datasetCount' => $datasetCount,
            'minDataset' => self::MIN_DATASET,
            'forceDatasetRegistration' => session('force_dataset_registration', false)
        ]);
    }

    public function store(Request $request)
    {
        DatasetWajah::create([
            'pegawai_id' => Auth::id(),
            'descriptor' => json_encode($request->descriptor)
        ]);

        return response()->json(['status' => true]);
    }

    public function load()
    {
        $pegawaiId = Auth::id();
        $datasets = DatasetWajah::with('pegawai')
            ->where('pegawai_id', $pegawaiId)
            ->get();

        $data = [];
        foreach ($datasets as $item) {
            $data[] = [
                'label' => $item->pegawai->nama . " | ID: " . $item->pegawai_id,
                'descriptor' => json_decode($item->descriptor)
            ];
        }

        return response()->json($data);
    }
}
```

#### 4. Controller Profil Pegawai (`app/Http/Controllers/Pegawai/ProfileController.php`)
```php
<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Pegawai;

class ProfileController extends Controller
{
    public function index()
    {
        $pegawai = Auth::user();
        $pegawai->load(['departemen', 'jadwal']);
        return view('pegawai.profile', ['pegawai' => $pegawai]);
    }

    public function updateProfile(Request $request)
    {
        $pegawai = Auth::user();
        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = ['nama' => $request->nama];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $filename = 'profile/' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($file));
            $updateData['foto'] = $filename;
        }

        Pegawai::where('id', $pegawai->id)->update($updateData);
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $pegawai = Auth::user();
        if (!Hash::check($request->current_password, $pegawai->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        Pegawai::where('id', $pegawai->id)->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
```

#### 5. Controller Dashboard Admin (`app/Http/Controllers/Admin/DashboardController.php`)
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $totalPegawai = Pegawai::where('status', 1)->count();
        $totalDepartemen = Departemen::count();

        $monthlyAttendances = Absensi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $totalAbsensiBulanIni = $monthlyAttendances->count();
        $tepatWaktuBulanIni = $monthlyAttendances->where('status', '!=', 'terlambat')->count();
        $terlambatBulanIni = $monthlyAttendances->where('status', 'terlambat')->count();

        $todayStr = Carbon::today()->toDateString();
        $todayAttendances = Absensi::whereDate('tanggal', $todayStr)->get();
        if ($todayAttendances->isEmpty() && $bulan != Carbon::now()->month) {
            $latestDate = Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->max('tanggal');
            if ($latestDate) {
                $todayAttendances = Absensi::whereDate('tanggal', $latestDate)->get();
            }
        }

        $hadirHariIni = $todayAttendances->count();
        $tepatWaktuHariIni = $todayAttendances->where('status', '!=', 'terlambat')->count();
        $terlambatHariIni = $todayAttendances->where('status', 'terlambat')->count();
        $belumAbsenHariIni = max(0, $totalPegawai - $hadirHariIni);

        $latestAbsensi = Absensi::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['pegawai.departemen', 'pegawai.jadwal'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->take(7)
            ->get();

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $chartLabels = [];
        $chartTepatWaktu = [];
        $chartTerlambat = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::createFromDate($tahun, $bulan, $d);
            if ($date->isWeekend()) continue;

            $dateStr = $date->toDateString();
            $chartLabels[] = $date->format('d M');

            $dailyAbsensi = $monthlyAttendances->filter(function($item) use ($dateStr) {
                return $item->tanggal === $dateStr;
            });

            $chartTepatWaktu[] = $dailyAbsensi->where('status', '!=', 'terlambat')->count();
            $chartTerlambat[] = $dailyAbsensi->where('status', 'terlambat')->count();
        }

        return view('admin.index', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'totalPegawai' => $totalPegawai,
            'totalDepartemen' => $totalDepartemen,
            'totalAbsensiBulanIni' => $totalAbsensiBulanIni,
            'tepatWaktuBulanIni' => $tepatWaktuBulanIni,
            'terlambatBulanIni' => $terlambatBulanIni,
            'hadirHariIni' => $hadirHariIni,
            'tepatWaktuHariIni' => $tepatWaktuHariIni,
            'terlambatHariIni' => $terlambatHariIni,
            'belumAbsenHariIni' => $belumAbsenHariIni,
            'latestAbsensi' => $latestAbsensi,
            'chartLabels' => $chartLabels,
            'chartTepatWaktu' => $chartTepatWaktu,
            'chartTerlambat' => $chartTerlambat,
        ]);
    }
}
```

#### 6. Controller Master Pegawai (`app/Http/Controllers/Admin/PegawaiController.php`)
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Departemen;
use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index()
    {
        $departemen = Departemen::all();
        $jadwal = JadwalKerja::all();
        return view('admin.pegawai.index', compact('departemen', 'jadwal'));
    }

    public function data()
    {
        $data = Pegawai::with('departemen', 'jadwal')->latest()->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('departemen', function ($d) { return $d->departemen->nama_departemen ?? '-'; })
            ->addColumn('jadwal', function ($d) { return $d->jadwal->nama_shift ?? '-'; })
            ->addColumn('aksi', function ($d) {
                return '<button onclick="editData(' . $d->id . ')" class="btn btn-warning btn-sm">Edit</button>
                        <button onclick="deleteData(' . $d->id . ')" class="btn btn-danger btn-sm">Delete</button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required',
            'departemen_id' => 'required',
            'jadwal_kerja_id' => 'required',
            'username' => 'required|unique:pegawais,username,' . $request->id,
        ];

        if (empty($request->id)) {
            $rules['password'] = 'required|min:4';
        }

        $request->validate($rules);

        if (!empty($request->id)) {
            $existingPegawai = Pegawai::find($request->id);
            $nik = $existingPegawai ? $existingPegawai->nik : $request->nik;
        } else {
            $yearMonth = date('Ym');
            $lastPegawai = Pegawai::where('nik', 'like', $yearMonth . '%')->latest('id')->first();
            if ($lastPegawai && preg_match('/' . $yearMonth . '(\d+)/', $lastPegawai->nik, $matches)) {
                $lastSeq = (int) $matches[1];
                $nik = $yearMonth . sprintf('%04d', $lastSeq + 1);
            } else {
                $count = Pegawai::count() + 1;
                $nik = $yearMonth . sprintf('%04d', $count);
            }
        }

        $status = ($request->status == '1' || $request->status === 1) ? 1 : 0;

        $data = [
            'nik' => $nik,
            'nama' => $request->nama,
            'departemen_id' => $request->departemen_id,
            'jadwal_kerja_id' => $request->jadwal_kerja_id,
            'jabatan' => $request->jabatan,
            'username' => $request->username,
            'status' => $status,
            'role' => $request->role ?? 'pegawai'
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        Pegawai::updateOrCreate(['id' => $request->id], $data);
        return response()->json(['status' => true, 'message' => 'Data pegawai berhasil disimpan']);
    }

    public function delete($id)
    {
        Pegawai::findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Data pegawai berhasil dihapus']);
    }
}
```

---

### BAB II: MODEL (ELOQUENT DATABASE STRUCTURE)

#### 1. Model Pegawai (`app/Models/Pegawai.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    use HasFactory;

    protected $table = 'pegawais';
    protected $fillable = [
        'nik', 'nama', 'foto', 'departemen_id', 'jadwal_kerja_id',
        'jabatan', 'username', 'password', 'role', 'status'
    ];
    protected $hidden = ['password'];

    public function departemen() { return $this->belongsTo(Departemen::class); }
    public function jadwal() { return $this->belongsTo(JadwalKerja::class, 'jadwal_kerja_id'); }
    public function dataset_wajahs() { return $this->hasMany(DatasetWajah::class, 'pegawai_id'); }
    public function absensis() { return $this->hasMany(Absensi::class, 'pegawai_id'); }
}
```

#### 2. Model Absensi (`app/Models/Absensi.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id', 'tanggal', 'jam_masuk', 'jam_pulang',
        'status', 'alasan_telat', 'alasan_pulang_awal', 'foto_masuk', 'foto_pulang'
    ];

    protected static function booted()
    {
        static::deleting(function ($absensi) {
            if ($absensi->foto_masuk && Storage::disk('public')->exists($absensi->foto_masuk)) {
                Storage::disk('public')->delete($absensi->foto_masuk);
            }
            if ($absensi->foto_pulang && Storage::disk('public')->exists($absensi->foto_pulang)) {
                Storage::disk('public')->delete($absensi->foto_pulang);
            }
        });
    }

    public function pegawai() { return $this->belongsTo(Pegawai::class, 'pegawai_id'); }
}
```

#### 3. Model Dataset Wajah (`app/Models/DatasetWajah.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetWajah extends Model
{
    use HasFactory;

    protected $table = 'dataset_wajahs';
    protected $fillable = ['pegawai_id', 'descriptor'];

    public function pegawai() { return $this->belongsTo(Pegawai::class, 'pegawai_id'); }
}
```

---

### BAB III: ROUTES (ATURAN ROUTING APLIKASI - `routes/web.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\JadwalKerjaController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\HariLiburController;
use App\Http\Controllers\Pegawai\DatasetPegawaiController;
use App\Http\Controllers\Pegawai\AbsensiPegawaiController;
use App\Http\Controllers\Pegawai\ProfileController;

Route::get('/', function () { return view('auth.login'); });
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ADMIN ROUTES */
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('departemen', [DepartemenController::class, 'index'])->name('departemen.index');
    Route::get('departemen/data', [DepartemenController::class, 'data'])->name('departemen.data');
    Route::post('departemen/store', [DepartemenController::class, 'store'])->name('departemen.store');
    Route::delete('departemen/delete/{id}', [DepartemenController::class, 'delete']);

    Route::get('jadwal', [JadwalKerjaController::class, 'index'])->name('jadwal.index');
    Route::get('jadwal/data', [JadwalKerjaController::class, 'data'])->name('jadwal.data');
    Route::post('jadwal/store', [JadwalKerjaController::class, 'store'])->name('jadwal.store');
    Route::delete('jadwal/delete/{id}', [JadwalKerjaController::class, 'delete']);

    Route::get('hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
    Route::post('hari-libur/store', [HariLiburController::class, 'store'])->name('hari-libur.store');
    Route::delete('hari-libur/delete/{id}', [HariLiburController::class, 'destroy'])->name('hari-libur.delete');

    Route::get('pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::get('pegawai/data', [PegawaiController::class, 'data'])->name('pegawai.data');
    Route::post('pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::delete('pegawai/delete/{id}', [PegawaiController::class, 'delete']);

    Route::get('dataset', [DatasetController::class, 'index'])->name('dataset.index');
    Route::delete('dataset/delete/{pegawaiId}', [DatasetController::class, 'destroy'])->name('dataset.delete');

    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('laporan/pegawai', [AbsensiController::class, 'laporanPegawai'])->name('laporan.pegawai');
    Route::get('laporan/keterlambatan', [AbsensiController::class, 'laporanKeterlambatan'])->name('laporan.keterlambatan');
    Route::get('laporan/pulang-cepat', [AbsensiController::class, 'laporanPulangCepat'])->name('laporan.pulang-cepat');
    Route::get('laporan/rekap-bulanan', [AbsensiController::class, 'laporanRekapBulanan'])->name('laporan.rekap-bulanan');
});

/* PEGAWAI ROUTES */
Route::prefix('pegawai')->middleware(['auth', 'role:pegawai'])->name('pegawai.')->group(function () {
    Route::get('/dashboard', [AbsensiPegawaiController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi', [AbsensiPegawaiController::class, 'index'])->name('absensi');
    Route::post('/absensi/store', [AbsensiPegawaiController::class, 'store'])->name('absensi.store');
    Route::get('/riwayat', [AbsensiPegawaiController::class, 'riwayat'])->name('riwayat');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/dataset', [DatasetPegawaiController::class, 'index'])->name('dataset');
    Route::post('/dataset/store', [DatasetPegawaiController::class, 'store'])->name('dataset.store');
});
```
