<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

class ContactController extends BaseResponseApi
{
    /**
     * Get all contacts
     */
    public function index(): JsonResponse
    {
        try {
            $data = Contact::latest()->get();
            return $this->sendResponse(['user' => $data], 'Data berhasil diambil', 200);
        } catch (\Throwable $th) {
            Log::error('Error getting  data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Store a newly created contact
     */
    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Jika ada foto, simpan ke storage dan dapatkan path-nya
            if ($request->hasFile('photo')) {
                // $manager = new ImageManager(Driver::class);
                $image = Image::read($request->file('photo'));
                $imageName = time() . '-' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();

                // Path penyimpanan
                $destinationPath = public_path('assets/image/contact/');
                $destinationPathThumbnail = public_path('assets/image/contact/thumbnails/');

                // Pastikan direktori ada
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                if (!file_exists($destinationPathThumbnail)) {
                    mkdir($destinationPathThumbnail, 0755, true);
                }

                // Simpan gambar utama
                $image->save($destinationPath . $imageName);

                // Simpan thumbnail
                $image->resize(100, 100);
                $image->save($destinationPathThumbnail . $imageName);

                $data['photo'] = $imageName;
            }

            $contact = Contact::create($data);
            return $this->sendResponse($contact, 'Contact created successfully', 201);
        } catch (\Throwable $th) {
            Log::error('Kesalahan saat menyimpan data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }
    /**
     * Show a specific contact
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            $data = Contact::where('uuid', $uuid)->firstOrFail();
            if (!$data) {
                return $this->sendResponse(null, 'Contact not found', false, 404);
            }
            return $this->sendResponse($data, 'Contact retrieved successfully', 200);
        } catch (\Throwable $th) {
            Log::error('Error getting  data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Update the specified contact
     */
    public function update(ContactRequest $request, string $uuid): JsonResponse
    {
        try {
            $contact = Contact::where('uuid', $uuid)->firstOrFail();
            $fileName = $contact->photo;
            $data = $request->validated();

            // Jika ada foto baru, simpan dan hapus foto lama
            if ($request->hasFile('photo')) {
                if ($fileName && file_exists(public_path('/assets/image/contact/' . $fileName))) {
                    unlink(public_path('/assets/image/contact/' . $fileName));
                    unlink(public_path('/assets/image/contact/thumbnails/' . $fileName));
                }

                $image = Image::read($request->file('photo'));
                $imageName = time() . '-' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();

                // Path penyimpanan
                $destinationPath = public_path('assets/image/contact/');
                $destinationPathThumbnail = public_path('assets/image/contact/thumbnails/');

                // Pastikan direktori ada
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                if (!file_exists($destinationPathThumbnail)) {
                    mkdir($destinationPathThumbnail, 0755, true);
                }

                // Simpan gambar utama
                $image->save($destinationPath . $imageName);

                // Simpan thumbnail
                $image->resize(100, 100);
                $image->save($destinationPathThumbnail . $imageName);
                $data['photo'] = $imageName;
            }

            $contact->update($data);
            return $this->sendResponse($contact, 'Contact updated successfully', 201);
        } catch (\Throwable $th) {
            Log::error('Kesalahan saat memperbarui data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Delete the specified contact (Soft Delete)
     */
    public function destroy(string $uuid): JsonResponse
    {
        try {
            $data = Contact::where('uuid', $uuid)->firstOrFail();
            if (!$data) {
                return $this->sendResponse(null, 'Contact not found', false, 404);
            }
            $data->delete();
            return $this->sendResponse(null, 'Contact deleted successfully', 200);
        } catch (\Throwable $th) {
            Log::error('Error getting  data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Restore soft deleted contact
     */
    public function restore(string $uuid): JsonResponse
    {
        try {
            $data = Contact::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            if (!$data) {
                return $this->sendResponse(null, 'Contact not found', false, 404);
            }
            $data->restore();
            return $this->sendResponse($data, 'Contact restored successfully', 200);
        } catch (\Throwable $th) {
            Log::error('Error getting  data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Permanently delete a contact
     */
    public function forceDelete(string $uuid): JsonResponse
    {
        try {
            $data = Contact::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            if (!$data) {
                return $this->sendResponse(null, 'Contact not found', false, 404);
            }
            $data->forceDelete();
            return $this->sendResponse(null, 'Contact permanently deleted', 200);
        } catch (\Throwable $th) {
            Log::error('Error getting  data: ' . $th->getMessage());
            return $this->sendServerError(
                $th->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }
}
