<?php

namespace App\Http\Controllers;

use App\Models\InsuranceCompany;
use App\Models\InsurancePrem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $suppliers = InsuranceCompany::all();
        return view('suppliers.index', ['suppliers' => $suppliers, 'notifications' => $notifications]);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'ins_id' => 'required|unique:insurance_companies,ins_id',
    //             'company' => 'required|unique:insurance_companies,company',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }

    //         InsuranceCompany::create([
    //             'ins_id' => $request->ins_id,
    //             'company' => $request->company,
    //             'status' => 1,
    //         ]);

    //         return response()->json(['success' => true, 'message' => 'Supplier has been created successfully!']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ins_id' => 'required|unique:insurance_companies,ins_id',
                'company' => 'required|unique:insurance_companies,company',
            ], [
                'company.unique' => 'The company name already exists in our database.',
                'ins_id.unique' => 'The insurance ID already exists in our database.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'duplicate_fields' => $validator->errors()->keys()
                ], 422);
            }

            InsuranceCompany::create([
                'ins_id' => $request->ins_id,
                'company' => $request->company,
                'status' => 1,
            ]);

            return response()->json(['success' => true, 'message' => 'Supplier has been created successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $supplier = InsuranceCompany::findOrFail($id);
            $supplier->status = $request->status;
            $supplier->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $supplier = InsuranceCompany::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ins_id' => 'required|unique:insurance_companies,ins_id,' . $id,
                'company' => 'required|unique:insurance_companies,company,' . $id,
            ], [
                'company.unique' => 'The company name already exists in our database.',
                'ins_id.unique' => 'The insurance ID already exists in our database.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                    'duplicate_fields' => $validator->errors()->keys()
                ], 422);
            }

            $supplier = InsuranceCompany::findOrFail($id);
            $supplier->update([
                'ins_id' => $request->ins_id,
                'company' => $request->company,
                'status' => $supplier->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Supplier has been updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Update error for supplier ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating supplier: ' . $e->getMessage()
            ], 500);
        }
    }
}
