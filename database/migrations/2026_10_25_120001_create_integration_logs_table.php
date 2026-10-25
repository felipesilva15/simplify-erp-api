<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 100)->nullable();
            $table->foreignId('connection_id')->nullable()->constrained('integration_connections')->nullOnDelete();
            $table->string('operation', 150)->nullable();
            $table->string('direction', 20); // outbound/inbound
            $table->string('method', 10)->nullable();
            $table->text('url_sanitized')->nullable();
            $table->integer('status_code')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->longText('request_masked')->nullable();
            $table->boolean('request_truncated')->default(false);
            $table->longText('response_masked')->nullable();
            $table->boolean('response_truncated')->default(false);
            $table->string('status', 20); // success/error/timeout
            $table->string('error_class', 255)->nullable();
            $table->string('correlation_id', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
    }
};
