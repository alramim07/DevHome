<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_views')) {
            Schema::table('project_views', function (Blueprint $table) {
                if (!Schema::hasColumn('project_views', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->useCurrent();
                }
                if (!Schema::hasColumn('project_views', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->useCurrent();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('project_views')) {
            Schema::table('project_views', function (Blueprint $table) {
                if (Schema::hasColumn('project_views', 'created_at')) {
                    $table->dropColumn('created_at');
                }
                if (Schema::hasColumn('project_views', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
