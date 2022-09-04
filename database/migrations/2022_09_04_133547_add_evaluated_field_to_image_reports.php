<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE image_reports MODIFY COLUMN probability ENUM('UNKNOWN', 'HIGH', 'MEDIUM', 'LOW', 'VERY_LOW') DEFAULT 'UNKNOWN'");
        Schema::table('image_reports', function (Blueprint $table) {
            $table->boolean('evaluated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE image_reports MODIFY COLUMN probability ENUM('HIGH', 'MEDIUM', 'LOW', 'VERY_LOW')");
        Schema::table('image_reports', function (Blueprint $table) {
            $table->dropColumn('evaluated');
        });
    }
};
