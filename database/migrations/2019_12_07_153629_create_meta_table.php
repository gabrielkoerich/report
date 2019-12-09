<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model');
            $table->string('name');
            $table->string('label');
            $table->string('type');
            $table->timestamps();
        });

        $metas = [
            [
                'model' => 'App\\Website',
                'name' => 'domain',
                'label' => 'Domain',
                'type' => 'text',
            ],
            [
                'model' => 'website',
                'name' => 'created_at',
                'label' => 'Created At',
                'type' => 'datetime',
            ],
            [
                'model' => 'website',
                'name' => 'updated_at',
                'label' => 'Updated At',
                'type' => 'datetime',
            ],
        ];

        foreach ($metas as $meta) {
            App\Meta::create($meta);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('meta');
    }
}
