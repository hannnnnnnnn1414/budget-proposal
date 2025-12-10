<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $currencies = Currency::all();
        return view('currencies.index', ['currencies' => $currencies, 'notifications' => $notifications]);
    }

    public function create()
    {
        return view('currencies.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cur_id' => 'required|unique:currencies,cur_id',
                'currency' => 'required|unique:currencies,currency',
            ], [
                'currency.unique' => 'The currency already exists in our database.',
                'cur_id.unique' => 'The ID already exists in our database.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'duplicate_fields' => $validator->errors()->keys()
                ], 422);
            }

            Currency::create([
                'cur_id' => $request->cur_id,
                'currency' => $request->currency,
                'nominal' => $request->nominal,
                'status' => 1,
            ]);

            return response()->json(['success' => true, 'message' => 'Currency has been created successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating currency: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $currency = Currency::findOrFail($id);
            $currency->status = $request->status;
            $currency->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('currencies.edit', compact('currency'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cur_id' => 'required|unique:currencies,cur_id',
                'currency' => 'required|unique:currencies,currency',
            ], [
                'currency.unique' => 'The currency already exists in our database.',
                'cur_id.unique' => 'The ID already exists in our database.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'duplicate_fields' => $validator->errors()->keys()
                ], 422);
            }

            $currency = Currency::findOrFail($id);
            $currency->update([
                'cur_id' => $request->cur_id,
                'currency' => $request->currency,
                'nominal' => $request->nominal,
                'status' => $currency->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Currency has been updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Update error for currency ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating currency: ' . $e->getMessage()
            ], 500);
        }
    }
}
