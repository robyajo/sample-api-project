<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseResponseApi
{
    /**
     * Register pengguna baru
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'min:3',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&_])[A-Za-z\d@$!%*#?&_]+$/',
            ],
            'c_password' => 'required|same:password',
        ], [
            'name.required' => 'Kolom nama tidak boleh kosong.',
            'name.max' => 'Nama tidak boleh lebih dari 200 karakter.',
            'email.required' => 'Kolom email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email yang Anda masukkan sudah terdaftar.',
            'password.required' => 'Kolom password tidak boleh kosong.',
            'password.min' => 'Password yang Anda masukkan minimal 3 karakter huruf dan angka.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu simbol.',
            'c_password.required' => 'Kolom konfirmasi password tidak boleh kosong.',
            'c_password.same' => 'Konfirmasi password yang Anda masukkan tidak sama. Silakan ulangi kembali.',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'user',
                'password' => Hash::make($request->password),
            ]);
            Profile::create([
                'user_id' => $user->id,
            ]);

            $token = Auth::login($user);
            DB::commit();
            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => [
                    'token' =>  $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]
            ];
            return $this->sendResponse($responseData, 'Registrasi berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registering user: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage(), 'Gagal melakukan registrasi');
        }
    }

    /**
     * Login pengguna
     * @unauthenticated
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'Form Alamat Email Tidak Boleh Kosong',
                'email.email' => 'Format Alamat Email Salah',
                'email.exists' => 'Alamat Email Tidak Terdaftar',
                'password.required' => 'Form Password Tidak Boleh Kosong',
                'password.min' => 'Password Minimal 6 Karakter',
            ]
        );

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        try {
            // Ambil user berdasarkan email
            if (!$token = Auth::attempt($request->only('email', 'password'))) {
                return $this->sendValidationError(['Password Salah']);
            }
            // Get the authenticated user.
            $user = Auth::user();
            // (optional) Attach the role to the token.
            $token = JWTAuth::claims(['role' => $user->email])->fromUser($user);

            $responseData = [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ],
            ];
            return $this->sendResponse($responseData, 'Login berhasil');
        } catch (\Throwable $e) {
            // Log error untuk keperluan debugging
            Log::error('Login Error: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage(), 'Gagal melakukan login');
        }
    }

    /**
     * Ambil data pengguna
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->sendResponse(null, 'User not found', false, 404);
            }
        } catch (JWTException $e) {
            return $this->sendResponse($e->getMessage(), 'invalid token', false, 400);
        } catch (\Throwable $th) {
            Log::error('Error getting user: ' . $th->getMessage());
            return $this->sendServerError($th->getMessage(), 'Terjadi kesalahan server', false, 500);
        }

        return $this->sendResponse($user, 'Berhasil mengambil data pengguna', true, 200);
    }


    /**
     * Refresh token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh()
    {
        try {
            $token = Auth::refresh();
            $responseData = [
                'access_token' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ];
            return $this->sendResponse($responseData, 'Token berhasil diperbarui');
        } catch (\Throwable $th) {
            Log::error('Error refreshing token: ' . $th->getMessage());
            return $this->sendServerError($th->getMessage(), 'Terjadi kesalahan server', false, 500);
        }
    }

    /**
     * Logout pengguna
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            // Auth::logout();
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->sendResponse(null, 'Logout berhasil');
        } catch (\Throwable $th) {
            Log::error('Logout Error: ' . $th->getMessage());
            return $this->sendServerError($th->getMessage(), 'Gagal melakukan logout', false, 500);
        }
    }

    /**
     * Cek session pengguna
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sessionCheck(Request $request)
    {
        try {
            // Cek keberadaan token
            if (!$request->bearerToken()) {
                return $this->sendResponse(null, 'Token tidak ditemukan', false, 401);
            }

            // Validasi token dan ambil user
            $token = JWTAuth::getToken();
            $user = JWTAuth::authenticate($token);

            if (!$user) {
                return $this->sendResponse(null, 'User tidak ditemukan', false, 401);
            }

            $responseData = [
                'user' => $user,
                'access_token' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]
            ];
            return $this->sendResponse($responseData, 'Session aktif', true, 200);
        } catch (TokenExpiredException $e) {
            return $this->sendResponse(null, 'Token telah kadaluarsa', false, 401);
        } catch (TokenInvalidException $e) {
            return $this->sendResponse(null, 'Token tidak valid', false, 401);
        } catch (JWTException $e) {
            return $this->sendResponse(null, 'Token tidak bisa diproses', false, 401);
        } catch (\Exception $e) {
            Log::error('Error checking session: ' . $e->getMessage());
            return $this->sendServerError($e->getMessage(), 'Terjadi kesalahan saat memeriksa session', false, 500);
        }
    }
}
