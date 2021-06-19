<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * CreatePermissionUserTable
 */
class CreatePermissionUserTable extends Migration
{
    private $table_name = 'permission_user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        if (! Schema::hasTable($this->table_name)) {
            Schema::create($this->table_name, function (Blueprint $table) {
                $table->foreignId('user_id');
                $table->foreignId('permission_id');
                $table->tinyInteger('actions', false, true)->default(0);
                $table->timestamps();
                $table->primary(['user_id', 'permission_id']);
//                $table->foreign('permission_id')
//                    ->references('id')
//                    ->on('permissions');
//                $table->foreign('user_id')
//                    ->references('id')
//                    ->on('users');
            });
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table_name);
        Schema::enableForeignKeyConstraints();
    }
}
