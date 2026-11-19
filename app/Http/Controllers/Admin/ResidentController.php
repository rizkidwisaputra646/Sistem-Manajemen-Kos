<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $today = \Carbon\Carbon::today()->toDateString();

        $query = Booking::with(['user', 'room'])
            ->where('status', 'success')
            ->where('check_out_date', '>=', $today);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $residents = $query->latest()->get();

        return view('admin.residents.index', compact('residents'));
    }
}
