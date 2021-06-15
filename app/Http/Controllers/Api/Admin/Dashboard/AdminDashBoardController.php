<?php

namespace App\Http\Controllers\Api\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashBoardController extends Controller
{
    public function analytics() {
        $total_users = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();

        return $total_users;
    }
}
