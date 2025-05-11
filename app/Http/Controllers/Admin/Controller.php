<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    protected function isAdmin()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function handleError(\Exception $e, $message = 'Terjadi kesalahan')
    {
        \Log::error($message . ': ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }

        return back()->with('error', $message);
    }
} 