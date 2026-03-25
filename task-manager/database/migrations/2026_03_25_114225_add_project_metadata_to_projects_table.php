<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {

            // Aggiungi deadline solo se non esiste
            if (!Schema::hasColumn('projects', 'deadline')) {
                $table->date('deadline')->nullable();
            }

        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {

            if (Schema::hasColumn('projects', 'deadline')) {
                $table->dropColumn('deadline');
            }

        });
    }
};
