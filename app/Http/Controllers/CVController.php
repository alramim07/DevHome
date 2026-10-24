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
            return redirect()->route('team', $slug)->with('error', "{$user->name} don't have any custom cv yet!");
        }

        $filePath = media_path($user->custom_cv);
        $diskPath = storage_path('app/public/' . $filePath);
        $pubPath = public_path(ltrim($user->custom_cv, '/'));

        if (file_exists($diskPath)) {
            return response()->download($diskPath, "CV-Of-{$user->name}.pdf");
        } elseif (file_exists($pubPath)) {
            return response()->download($pubPath, "CV-Of-{$user->name}.pdf");
        }

        return redirect()->route('team', $slug)->with('error', 'The file was not found on the server.');
    }
}
