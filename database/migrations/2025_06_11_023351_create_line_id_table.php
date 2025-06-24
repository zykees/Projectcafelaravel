<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'line_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('line_id')->nullable()->after('google_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'line_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('line_id');
            });
        }
    }
};