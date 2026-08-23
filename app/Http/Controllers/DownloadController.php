<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    // Handle secure file download by token
    public function download($token)
    {
        // Find the download permission
        $download = Download::where('token', $token)->with('product')->firstOrFail();

        // Validate expiry and count limits
        if (!$download->isValid()) {
            return redirect()->route('home')
                ->with('error', 'عذراً، هذا الرابط منتهي الصلاحية أو تم استنفاد الحد الأقصى لمرات التحميل.');
        }

        $product = $download->product;

        if (!$product || !$product->digital_file_path) {
            abort(404, 'الملف غير متوفر حالياً.');
        }

        // Increment download counter
        $download->increment('download_count');

        // Retrieve file from private storage disk
        $filePath = $product->digital_file_path; // e.g. private_downloads/sample.pdf
        $downloadName = $product->digital_file_name ?: basename($filePath);

        // Check file existence
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'ملف التحميل غير موجود على الخادم. يرجى التواصل مع الدعم الفني.');
        }

        // Return download stream response
        return Storage::disk('local')->download($filePath, $downloadName);
    }
}
