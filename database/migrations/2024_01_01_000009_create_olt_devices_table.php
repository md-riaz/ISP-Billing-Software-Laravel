<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('olt_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('device_name');
            $table->string('vendor')->nullable();
            $table->string('model')->nullable();
            $table->string('base_url')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->enum('auth_type', ['api_key','basic','token'])->default('api_key');
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->text('api_token')->nullable();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pop_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('olt_devices'); }
};
