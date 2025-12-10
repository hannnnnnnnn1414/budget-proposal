<?php

namespace App\Http\Controllers;

use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\Lembur\Department;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npk' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('npk', 'password');
        if (!Auth::attempt($credentials)) {
            return redirect()->back()
                ->withErrors(['npk' => 'Incorrect NPK or Password, please try again.'])
                ->withInput();
        }

        $user = Auth::user();
        $departments = $user->department ? $user->department->department : 'Tidak Diketahui';
        session([
            'npk' => $user->npk,
            'dept' => $user->dept,
            'sect' => $user->sect,
            'name' => $user->name,
            'department' => $departments,
        ]);

        Log::info('User Logged In', [
            'npk' => $user->npk,
            'dept' => $user->dept,
            'sect' => $user->sect,
            'name' => $user->name,
            'department' => $departments
        ]);

        $otp = 123456;
        $expiryDate = Carbon::now()->addMinutes(5);

        $otpRecord = OtpVerification::where('id_user', Auth::id())->first();

        if ($otpRecord) {
            $otpRecord->update([
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => '2',
                'use' => '2',
                'use_date' => null,
            ]);
        } else {
            OtpVerification::create([
                'id_user' => Auth::id(),
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => '2',
                'use' => '2',
            ]);
        }

        session(['otp_expiry' => $expiryDate]);

        return redirect()->route('otp.otp-verification');
    }
}
