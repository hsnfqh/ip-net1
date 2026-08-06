<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

    public function uploadCertification(Request $request)
    {
        $request->validate([
            'certification_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'certification_file.required' => 'Silakan pilih file sertifikasi untuk diupload.',
            'certification_file.mimes'    => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'certification_file.max'      => 'Ukuran file maksimal adalah 5MB.',
        ]);

        $user = auth()->user();

        if ($request->hasFile('certification_file')) {
            // Hapus sertifikasi lama jika ada
            if ($user->certification_file && Storage::disk('public')->exists($user->certification_file)) {
                Storage::disk('public')->delete($user->certification_file);
            }

            $path = $request->file('certification_file')->store('certifications', 'public');
            
            $user->certification_file = $path;
            $user->certification_status = 'pending';
            $user->certification_uploaded_at = now();
            $user->save();
        }

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sertifikasi berhasil diupload! Menunggu verifikasi Lead Engineer.',
                'user'    => [
                    'certification_file'        => $user->certification_file,
                    'certification_status'      => $user->certification_status,
                    'certification_uploaded_at' => $user->certification_uploaded_at->format('d M Y H:i'),
                ]
            ]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Sertifikasi berhasil diupload! Menunggu verifikasi Lead Engineer.');
    }
}
