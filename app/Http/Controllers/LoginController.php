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
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        // Validate user credentials and CAPTCHA
        $validator = Validator::make($request->all(), [
            'npk' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha', // Validate CAPTCHA
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate user credentials
        $credentials = $request->only('npk', 'password');
        if (!Auth::attempt($credentials)) {
            return redirect()->back()
                ->withErrors(['npk' => 'Incorrect NPK or Password, please try again.'])
                ->withInput();
        }

        $user = Auth::user();
        $departments = $user->department ? $user->department->department : 'Tidak Diketahui';
        // Simpan npk ke session
        session([
            'npk' => $user->npk, // Tambahkan npk
            'dept' => $user->dept,
            'sect' => $user->sect,
            'name' => $user->name,
            'department' => $departments,
        ]);

        // Debug: Log session data
        Log::info('User Logged In', [
            'npk' => $user->npk,
            'dept' => $user->dept,
            'sect' => $user->sect,
            'name' => $user->name,
            'department' => $departments
        ]);

        // Generate OTP
        // $otp = rand(100000, 999999);
        $otp = 123456;
        $expiryDate = Carbon::now()->addMinutes(5); // Set OTP expiry to 5 minutes

        $otpRecord = OtpVerification::where('id_user', Auth::id())->first();

        if ($otpRecord) {
            // Update existing record
            $otpRecord->update([
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => 'db',
                'use' => 'unused',
                'use_date' => null,
            ]);
        } else {
            // Create new record
            OtpVerification::create([
                'id_user' => Auth::id(),
                'otp' => $otp,
                'expiry_date' => $expiryDate,
                'send' => 'db',
                'use' => 'unused',
            ]);
        }

        // Store OTP expiry in session for frontend timer
        session(['otp_expiry' => $expiryDate]);

        return redirect()->route('otp.otp-verification');
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
