<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('synced_onus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->string('onu_identifier')->nullable();
            $table->string('onu_serial')->nullable();
            $table->string('onu_name')->nullable();
            $table->string('pon_port')->nullable();
            $table->string('status')->nullable();
            $table->string('signal_level')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('synced_onus'); }
};
