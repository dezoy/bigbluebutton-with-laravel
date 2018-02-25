<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasTable('subscribers') ) {
            Schema::create('course_subscribers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('meeting_id');
                $table->integer('user_id');

				$table->timestamps();
	            $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subscribers');
    }
}
