<?php

namespace Tests\Unit;

use Tests\TestCase;

class MediaHelperTest extends TestCase
{
    public function test_media_path_strips_the_legacy_storage_prefix(): void
    {
        $this->assertSame('project_thumbnail/a.jpg', media_path('storage/project_thumbnail/a.jpg'));
        $this->assertSame('project_thumbnail/a.jpg', media_path('/storage/project_thumbnail/a.jpg'));
        $this->assertSame('web-settings/logo.png', media_path('web-settings/logo.png'));
    }

    public function test_media_path_returns_null_for_empty_values(): void
    {
        $this->assertNull(media_path(null));
        $this->assertNull(media_path(''));
        $this->assertNull(media_path('   '));
    }

    public function test_media_url_passes_absolute_urls_through_untouched(): void
    {
        $this->assertSame('https://cdn.example.com/a.jpg', media_url('https://cdn.example.com/a.jpg'));
        $this->assertSame('//cdn.example.com/a.jpg', media_url('//cdn.example.com/a.jpg'));
    }

    public function test_media_url_resolves_a_relative_path_without_doubling_the_storage_prefix(): void
    {
        $url = media_url('storage/project_thumbnail/a.jpg');

        $this->assertStringContainsString('project_thumbnail/a.jpg', $url);
        $this->assertStringNotContainsString('storage/storage', $url);
    }

    public function test_media_url_is_null_for_empty_values(): void
    {
        $this->assertNull(media_url(null));
        $this->assertNull(media_url(''));
    }
}
