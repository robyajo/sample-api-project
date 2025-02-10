<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseResponseApi
{
    /**
     * User Index
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $role = $request->query('role');
            $perPage = $request->query('per_page', 10); // ✅ Default 10 jika tidak ada input

            // Pastikan perPage adalah angka positif
            $perPage = is_numeric($perPage) && $perPage > 0 ? (int) $perPage : 10;

            $query = User::select('id', 'uuid', 'name', 'email', 'role');

            // 🔍 Filter berdasarkan pencarian nama atau email
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // 🎭 Filter berdasarkan role jika ada
            if (!empty($role)) {
                $query->where('role', $role);
            }

            // 📌 Pagination dengan per_page yang bisa dikustomisasi
            $data = $query->paginate($perPage);

            return $this->sendResponse(['user' => $data], 'Data berhasil diambil', 200);
        } catch (\Exception $e) {
            Log::error('Error getting user data: ' . $e->getMessage());
            return $this->sendServerError(
                $e->getMessage() ?? 'Gagal mengambil data',
                'Terjadi kesalahan server',
                false,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return $this->sendResponse($user, 'User created successfully.', 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage() ?? 'Gagal membuat user', 'Terjadi kesalahan server', false, 500);
        }
    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        try {
            $user = User::where('uuid', $id)->first();
            if (! $user) {
                return $this->sendResponse(null, 'User not found', false, 404);
            }

            return $this->sendResponse($user, 'User retrieved successfully.', 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage() ?? 'Gagal mengambil user', 'Terjadi kesalahan server', false, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|string|in:admin,user',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            $user = User::where('uuid', $id)->first();

            if (!$user) {
                return $this->sendResponse(null, 'User not found', false, 404);
            }
            $user->update($request->all());

            return $this->sendResponse($user, 'User updated successfully.', 200);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage() ?? 'Gagal memperbarui user', 'Terjadi kesalahan server', false, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $user = User::where('uuid', $id)->first();

            if (!$user) {
                return $this->sendResponse(null, 'User not found', false, 404);
            }

            $user->delete();

            return $this->sendResponse(null, 'User deleted successfully.', 200);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage() ?? 'Gagal menghapus user', 'Terjadi kesalahan server', false, 500);
        }
    }
}
