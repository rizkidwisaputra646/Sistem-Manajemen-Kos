<?php

namespace App\Providers;

use App\Models\DamageReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share notifikasi laporan kerusakan ke view layout
        View::composer(['layouts.app', 'layouts.sidebars.admin', 'layouts.sidebars.user', 'layouts.sidebars.user-bottom', 'dashboard'], function ($view) {
            $adminNewReportsCount = 0;
            $adminRecentReports = collect();
            $userUnreadReportsCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    if (Schema::hasTable('damage_reports')) {
                        $adminNewReportsCount = DamageReport::where('status', 'Menunggu')->count();
                        $adminRecentReports = DamageReport::with(['user', 'room'])
                            ->where('status', 'Menunggu')
                            ->orWhere('is_read_by_admin', false)
                            ->latest()
                            ->take(5)
                            ->get();
                    }
                } else {
                    if (Schema::hasTable('damage_reports')) {
                        $userUnreadReportsCount = DamageReport::where('user_id', $user->id)
                            ->where('is_read_by_user', false)
                            ->count();
                    }
                }
            }

            $view->with('adminNewReportsCount', $adminNewReportsCount);
            $view->with('adminRecentReports', $adminRecentReports);
            $view->with('userUnreadReportsCount', $userUnreadReportsCount);
        });
    }
}
