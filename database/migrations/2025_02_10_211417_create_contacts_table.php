<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis
            $table->uuid('uuid')->unique(); // Kolom UUID
            $table->string('photo')->nullable(); // Foto kontak
            $table->string('name'); // Nama kontak
            $table->string('email')->unique(); // Email kontak (unik)
            $table->string('phone'); // Nomor telepon
            $table->string('city'); // Kota (opsional)
            $table->string('zip_code'); // Kode pos (opsional)
            $table->string('country'); // Negara (opsional)
            $table->text('address')->nullable(); // Alamat (opsional)
            $table->text('notes')->nullable(); // Catatan (opsional)
            $table->softDeletes();
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
