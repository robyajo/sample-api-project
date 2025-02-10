<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), // Nama acak
            'email' => fake()->unique()->safeEmail(), // Email acak (unik)
            'phone' => fake()->phoneNumber(), // Nomor telepon acak
            'city' => fake()->city(), // Kota acak
            'country' => fake()->country(), // Negara acak
            'zip_code' => fake()->postcode(), // Kode pos acak
            'address' => fake()->address(), // Alamat acak
            'notes' => fake()->sentence(), // Catatan acak
        ];
    }
}
