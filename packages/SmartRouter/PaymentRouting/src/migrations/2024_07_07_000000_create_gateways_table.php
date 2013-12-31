<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewaysTable extends Migration
{
    public function up()
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('details');

            // $table->string('name');
            // $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
