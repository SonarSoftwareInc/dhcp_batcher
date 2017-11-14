<?php

namespace App\Http\Controllers;

use App\PendingDhcpAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pendingCount = PendingDhcpAssignment::count();
        $oldestSpan = 0;

        if ($pendingCount > 0)
        {
            $oldest = PendingDhcpAssignment::orderBy('created_at','desc')->first();
            if ($oldest !== null)
            {
                $now = Carbon::now();
                $oldestTime = new Carbon($oldest->created_at);
                $oldestSpan = $now->diffInMinutes($oldestTime);
            }
        }
        return view('home', compact('pendingCount','oldestSpan'));
    }
}
