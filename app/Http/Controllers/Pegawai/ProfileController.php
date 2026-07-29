<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
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
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $pegawai = Auth::user();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Delete old photo if exists
            if ($pegawai->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pegawai->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto);
            }

            // Upload new photo
            $filename = 'profile/' . $pegawai->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, file_get_contents($file));

            Pegawai::where('id', $pegawai->id)->update([
                'foto' => $filename
            ]);
        }

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }
}
