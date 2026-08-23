<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function markRead(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        return response()->json(['success' => true]);
    }
}
