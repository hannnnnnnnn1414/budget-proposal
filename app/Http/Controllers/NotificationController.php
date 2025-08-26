<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Approval;
use App\Models\User;
use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $user = Auth::user();
        
        // Ambil notifikasi dari database
        $notifications = Notification::where('npk', $user->npk)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'message' => $notification->message,
                    'created_at' => $notification->created_at,
                    'is_read' => $notification->is_read,
                    'sub_id' => $notification->sub_id
                ];
            })
            ->toArray();

        return $notifications;
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->npk == Auth::user()->npk) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    // Method untuk membuat notifikasi baru
    public static function createNotification($npk, $message, $subId = null)
    {
        // Ambil nomor HP dari database ISD
        $noHp = DB::connection('mysql2') // Sesuaikan dengan nama koneksi database kedua
            ->table('hp')
            ->where('npk', $npk)
            ->value('no_hp');

        return Notification::create([
            'npk' => $npk,
            'message' => $message,
            'sub_id' => $subId,
            'is_read' => false,
            'no_hp' => $noHp // Masukkan nomor HP ke dalam notifikasi
        ]);
    }


    public function deleteAll()
    {
        $user = Auth::user();
        try {
            Notification::where('npk', $user->npk)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to delete all notifications for NPK {$user->npk}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete notifications'], 500);
        }
    }
}