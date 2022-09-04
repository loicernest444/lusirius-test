<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('image_reports', function (Blueprint $table) {
            $table->string('callback')->nullable();
            $table->enum('probability', ['HIGH', 'MEDIUM', 'LOW', 'VERY_LOW']);
            $table->boolean('approved')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            $table->dropColumn(['callback', 'probability', 'approved']);
        });
    }
};
