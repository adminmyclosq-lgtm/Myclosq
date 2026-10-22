<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_sections', function (Blueprint $table) {
            $table->text('subtitle')->nullable()->change();
            if (!Schema::hasColumn('cms_sections', 'image_position')) {
                $table->string('image_position', 20)->default('right')->after('media_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_sections', function (Blueprint $table) {
            if (Schema::hasColumn('cms_sections', 'image_position')) {
                $table->dropColumn('image_position');
            }
            $table->string('subtitle', 500)->nullable()->change();
        });
    }
};
