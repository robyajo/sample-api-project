<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $this->contact,
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 255 karakter.',
            'zip_code.string' => 'Kode pos harus berupa teks.',
            'zip_code.max' => 'Kode pos maksimal 20 karakter.',
            'country.string' => 'Negara harus berupa teks.',
            'country.max' => 'Negara maksimal 255 karakter.',
            'address.string' => 'Alamat harus berupa teks.',
            'notes.string' => 'Catatan harus berupa teks.',
        ];
    }
}
