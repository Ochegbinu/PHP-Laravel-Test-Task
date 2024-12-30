<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends BaseAPIController
{
public function getLatest(Request $request)
{
    $since = $request->query('since');
    
    return Notification::where('user_id', auth()->id())
        ->where('created_at', '>', $since)
        ->orderBy('created_at', 'desc')
        ->get();
}
}
