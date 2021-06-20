<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreatePermissionRoleTable
 */
class CreatePermissionRoleTable extends Migration
{
    private $table_name = 'permission_role';

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
                $table->foreignId('role_id');
                $table->foreignId('permission_id');
                $table->tinyInteger('actions')->default(0);
                $table->primary(['role_id', 'permission_id']);
                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
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
