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
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        if (! Schema::hasTable($this->table_name)) {
            Schema::create($this->table_name, function (Blueprint $table) {
                $table->bigInteger('user_id', false, true);
                $table->integer('permission_id')->unsigned();
                $table->tinyInteger('actions')->default(0);
                $table->timestamps();
                $table->foreign('permission_id')->references('id')->on('permissions');
                $table->primary(['user_id', 'permission_id']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table_name);
        Schema::enableForeignKeyConstraints();
    }
}
