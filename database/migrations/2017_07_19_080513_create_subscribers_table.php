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
            Schema::create('subscribers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('hash')->default('');
                $table->integer('meeting_id')->default(0);
                $table->integer('subscriber_id')->default(0);
                $table->tinyInteger('isModerator')->default(0);
                $table->string('fullname')->default('');

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
