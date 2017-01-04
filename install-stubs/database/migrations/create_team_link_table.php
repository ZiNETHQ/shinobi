<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_link', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent');
            $table->unsignedInteger('child');
            $table->string('role', 20);
            $table->timestamps();

            $table->foreign('parent')->references('id')->on(Spark::team()->table())->onDelete('cascade');
            $table->foreign('child')->references('id')->on(Spark::team()->table())->onDelete('cascade');
            $table->unique(['parent', 'child']);
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('team_link');
    }
}
