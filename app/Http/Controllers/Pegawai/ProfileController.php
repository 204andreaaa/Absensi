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

        return view('pegawai.profile', [
            'pegawai' => $pegawai
        ]);
    }

    public function updateProfile(Request $request)
    {
        $pegawai = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $updateData = [
            'nama' => $request->nama,
        ];

        // Process photo upload if provided
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Delete old photo if exists
            if ($pegawai->foto && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            // Upload new photo
            $filename = 'profile/' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($file));

            $updateData['foto'] = $filename;
        }

        Pegawai::where('id', $pegawai->id)->update($updateData);

        return back()->with('success', 'Profil dan nama berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $pegawai = Auth::user();

        if (!Hash::check($request->current_password, $pegawai->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        Pegawai::where('id', $pegawai->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }

    public function updateFoto(Request $request)
    {
        return $this->updateProfile($request);
    }
}
