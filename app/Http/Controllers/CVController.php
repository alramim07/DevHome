<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\User;
use Spatie\LaravelPdf\Facades\Pdf;

class CVController extends Controller
{
    /**
     * 1. Generate DevHome CV (Dynamic PDF)
     */
    public function generateDevHome($slug)
    {
        $user = User::where('slug', $slug)->firstOrFail();
        $data = [
            'user' => $user,
            'projects' => Projects::where('user_id', $user->id)->get()
        ];

        try {
            return Pdf::view('pdf.dev_cv', $data)
                ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot) {
                    $browsershot->noSandbox()
                        ->addChromiumArguments([
                            'no-sandbox',
                            'disable-setuid-sandbox',
                            'disable-dev-shm-usage',
                            'disable-gpu',
                        ]);

                    $chromePath = env('CHROME_PATH', env('PUPPETEER_EXECUTABLE_PATH'));
                    if (!$chromePath && file_exists('/usr/bin/chromium-browser')) {
                        $chromePath = '/usr/bin/chromium-browser';
                    } elseif (!$chromePath && file_exists('/usr/bin/chromium')) {
                        $chromePath = '/usr/bin/chromium';
                    }

                    if ($chromePath) {
                        $browsershot->setChromePath($chromePath);
                    }

                    if ($nodeBinary = env('NODE_BINARY')) {
                        $browsershot->setNodeBinary($nodeBinary);
                    } elseif (file_exists('/usr/bin/node')) {
                        $browsershot->setNodeBinary('/usr/bin/node');
                    }

                    if ($npmBinary = env('NPM_BINARY')) {
                        $browsershot->setNpmBinary($npmBinary);
                    } elseif (file_exists('/usr/bin/npm')) {
                        $browsershot->setNpmBinary('/usr/bin/npm');
                    }
                })
                ->name("Cv-Of-" . \Illuminate\Support\Str::slug($user->name) . ".pdf")
                ->download();
        } catch (\Throwable $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean fallback: render CV view directly for the user
            return response()->view('pdf.dev_cv', $data);
        }
    }

    /**
     * 2. Download Customized CV (Stored File)
     */
    public function downloadCustom($slug)
    {
        $user = User::where('slug', $slug)->firstOrFail();

        if (empty($user->custom_cv)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => "{$user->name} don't have any custom cv yet!"], 404);
            }
            return redirect()->route('team', $slug)->with('error', "{$user->name} don't have any custom cv yet!");
        }

        $downloadName = "CV-Of-" . \Illuminate\Support\Str::slug($user->name) . ".pdf";

        // 1. If stored as an absolute URL (e.g. Supabase bucket)
        if (\Illuminate\Support\Str::startsWith($user->custom_cv, ['http://', 'https://'])) {
            try {
                $fileContent = @file_get_contents($user->custom_cv);
                if ($fileContent !== false) {
                    return response($fileContent, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                        'Content-Length' => strlen($fileContent),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Remote CV stream failed: ' . $e->getMessage());
            }
            return redirect()->away($user->custom_cv);
        }

        $filePath = media_path($user->custom_cv);

        // 2. Storage facade (works for both local disk and S3/Supabase storage on Render)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($filePath, $downloadName);
        }

        // 3. Fallback: local storage directory
        $diskPath = storage_path('app/public/' . $filePath);
        if (file_exists($diskPath)) {
            return response()->download($diskPath, $downloadName);
        }

        // 4. Fallback: public path
        $pubPath = public_path(ltrim($user->custom_cv, '/'));
        if (file_exists($pubPath)) {
            return response()->download($pubPath, $downloadName);
        }

        // 5. Fallback: resolve via media_url() helper
        $remoteUrl = media_url($user->custom_cv);
        if ($remoteUrl && \Illuminate\Support\Str::startsWith($remoteUrl, ['http://', 'https://'])) {
            try {
                $fileContent = @file_get_contents($remoteUrl);
                if ($fileContent !== false) {
                    return response($fileContent, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                        'Content-Length' => strlen($fileContent),
                    ]);
                }
            } catch (\Throwable $e) {}
            return redirect()->away($remoteUrl);
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['error' => 'The file was not found on the server.'], 404);
        }

        return redirect()->route('team', $slug)->with('error', 'The file was not found on the server.');
    }
}
