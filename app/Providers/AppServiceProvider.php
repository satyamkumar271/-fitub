<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $sidebarUnreadCount = 0;

            if (!Auth::check()) {
                $view->with('sidebarUnreadCount', $sidebarUnreadCount);
                return;
            }

            $user = Auth::user();
            if (!in_array($user->user_type, ['customer', 'trainer', 'gymowner'], true)) {
                $view->with('sidebarUnreadCount', $sidebarUnreadCount);
                return;
            }
            if (!Schema::hasTable('inquiries') || !Schema::hasTable('inquiry_messages') || !Schema::hasTable('inquiry_read_states')) {
                $view->with('sidebarUnreadCount', $sidebarUnreadCount);
                return;
            }

            $query = DB::table('inquiry_messages as m')
                ->join('inquiries as i', 'i.id', '=', 'm.inquiry_id')
                ->leftJoin('inquiry_read_states as rs', function ($join) use ($user) {
                    $join->on('rs.inquiry_id', '=', 'm.inquiry_id')
                        ->where('rs.user_id', '=', $user->id);
                })
                ->where('m.sender_id', '!=', $user->id)
                ->where(function ($q) {
                    $q->whereNull('rs.last_read_at')
                        ->orWhereColumn('m.created_at', '>', 'rs.last_read_at');
                });

            if ($user->user_type === 'customer') {
                $query->where('i.user_id', $user->id);
            } else {
                $query->where('i.recipient_id', $user->id)
                    ->whereIn('i.status', ['forwarded', 'viewed']);
            }

            $sidebarUnreadCount = (int) $query->count();

            $view->with('sidebarUnreadCount', $sidebarUnreadCount);
        });
    }
}
