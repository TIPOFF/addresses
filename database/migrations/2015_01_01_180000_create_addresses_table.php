<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tipoff\Addresses\Models\DomesticAddress;
use Tipoff\Addresses\Models\Phone;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DomesticAddress::class)->index();
            $table->morphs('addressable');
            $table->string('type');     // Shipping, Billing enums
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('care_of')->nullable();
            $table->string('company')->nullable();
            $table->string('extended_zip')->nullable();
            $table->foreignIdFor(Phone::class)->nullable();
            $table->foreignIdFor(app('user'), 'creator_id');
            $table->foreignIdFor(app('user'), 'updater_id');
            $table->timestamps();

            $table->unique(['domestic_address_id', 'addressable_id', 'addressable_type', 'type'], 'address_unique_key');
        });
    }
}