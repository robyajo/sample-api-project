<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseResponseApi extends Controller
{
    /**
     * Metode untuk mengirim respons sukses.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($data = null, $message = 'Sukses', $status = true, $code = 200)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Metode untuk mengirim respons error.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($message = 'Error', $errors = null, $code = 400)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Metode untuk mengirim respons validasi error.
     *
     * @param mixed $errors
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendValidationError($errors, $message = 'Validasi gagal')
    {
        return $this->sendError($message, $errors, 422);
    }

    /**
     * Metode untuk mengirim respons unauthorized.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendUnauthorized($message = 'Unauthorized')
    {
        return $this->sendError($message, null, 401);
    }

    /**
     * Metode untuk mengirim respons forbidden.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendForbidden($message = 'Forbidden')
    {
        return $this->sendError($message, null, 403);
    }

    /**
     * Metode untuk mengirim respons not found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotFound($message = 'Data tidak ditemukan')
    {
        return $this->sendError($message, null, 404);
    }

    /**
     * Metode untuk mengirim respons server error.
     *
     * @param string $message
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendServerError($message = 'Terjadi kesalahan pada server', $errors = null)
    {
        return $this->sendError($message, $errors, 500);
    }
}
