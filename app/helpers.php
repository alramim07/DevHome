<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('media_path')) {
    /**
     * Normalise a value stored in the database into a disk-relative path.
     *
     * The app historically stored media either with a "storage/" prefix
     * (e.g. "storage/project_thumbnail/abc.jpg") or without one
     * (e.g. "web-settings/logo.png"). The "public" disk root already points at
     * storage/app/public, so the prefix has to be stripped before using the
     * storage facade.
     */
    function media_path(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        // Already an absolute URL - nothing to normalise for the disk.
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return preg_replace('#^/?(?:storage/)+#', '', ltrim($path));
    }
}

if (! function_exists('media_url')) {
    /**
     * Resolve a stored media value to a browser-friendly URL.
     *
     * Works whether the "public" disk is the local filesystem (development)
     * or S3-compatible object storage (production, e.g. Supabase).
     */
    function media_url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        // Absolute URLs are returned untouched (asset() also passes them through).
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return Storage::disk('public')->url(media_path($path));
    }
}
