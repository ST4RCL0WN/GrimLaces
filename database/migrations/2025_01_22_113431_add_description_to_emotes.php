<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('emotes', function (Blueprint $table) {
            //
            $table->text('description')->nullable()->default(null)->after('name');
            $table->string('alt_text')->nullable()->default(null)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('emotes', function (Blueprint $table) {
            //
            $table->dropColumn('description');
            $table->dropColumn('alt_text');
        });
    }
};
