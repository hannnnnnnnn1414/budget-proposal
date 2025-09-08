<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OtpController extends Controller
{
    public function otpVerif()
    {
        return view('otp.otp-verification');
    }


    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $otpVerification = OtpVerification::where('id_user', Auth::id())
            ->where('otp', $request->otp)
            ->where('use', '2')
            ->where('expiry_date', '>', now())
            ->first();

        if ($otpVerification) {
            $otpVerification->update([
                'use' => '1',
                'use_date' => now(),
            ]);

            $dept = session('dept');
            $sect = session('sect');

            if ($dept === '6121' && in_array($sect, ['Kadept', 'PIC'])) {
                return redirect()->route('index');
            } elseif ($sect === 'Kadept' && $dept !== '6121') {
                return redirect()->route('index-all');
            } elseif ($sect === 'Kadiv') {
                return redirect()->route('index-all');
            } elseif ($sect === 'DIC') {
                return redirect()->route('index-all');
            } elseif (!in_array($sect, ['Kadept', 'Kadiv', 'DIC']) && !($dept === '6121' && in_array($sect, ['Kadept', 'PIC']))) {
                return redirect()->route('index-all');
            }
        }

        return back()->withErrors(['error' => 'OTP tidak valid atau sudah kadaluarsa']);
    }

    public function resendOtp()
    {
        $user = Auth::user();

        // Check if an unexpired OTP exists
        $existingOtp = OtpVerification::where('id_user', $user->id)
            ->where('use', 'unused')
            ->where('expiry_date', '>=', Carbon::now())
            ->first();

        if ($existingOtp) {
            return redirect()->route('otp.otp-verification')->with('error', 'Tunggu hingga OTP saat ini kadaluarsa sebelum meminta OTP baru.');
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $expiryDate = Carbon::now()->addMinutes(5);

        $otpRecord = OtpVerification::where('id_user', $user->id)->first();

        if ($otpRecord) {
            // Update existing record
            $otpRecord->update([
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => '2',
                'send_date' => Carbon::now(),
                'use' => '2',
                'use_date' => null,
            ]);
        } else {
            // Create new record
            OtpVerification::create([
                'id_user' => $user->id,
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => '2',
                'send_date' => Carbon::now(),
                'use' => '2',
                'use_date' => null,
            ]);
        }
        // OtpVerification::create([
        //     'id_user' => $user->id,
        //     'otp' => $otp,
        //     'expiry_date' => $expiryDate,
        //     'send' => 'sent',
        //     'send_date' => Carbon::now(),
        //     'use' => 'unused',
        //     'use_date' => null,
        // ]);

        // Update session with new OTP expiry
        session(['otp_expiry' => $expiryDate]);

        return redirect()->route('otp.otp-verification')->with('message', 'OTP berhasil dikirim ulang.');
    }
}
