<?php

namespace App\Http\Controllers;

use App\Models\AccountBudget;
use App\Models\BudgetCode;
use App\Models\Departments;
use App\Models\Dimension;
use App\Models\LineOfBusiness;
use App\Models\Workcenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DimensionController extends Controller
{
    public function index()
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $dimensions = Dimension::all();
        return view('master.index', ['dimensions' => $dimensions, 'notifications' => $notifications]);
    }

    public function detail($dim_id)
    {
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();
        $dimensions = Dimension::where('dim_id', $dim_id)->firstOrFail();
        $data = [];

        switch ($dim_id) {
            case 1:
                $data = LineOfBusiness::all();
                break;
            case 2:
                $data = Departments::all();
                break;
            case 3:
                $data = Workcenter::all();
                break;
            case 4:
                $data = BudgetCode::all();
                break;
            default:
                // Jika dim_id tidak valid
                return redirect()->back()->with('error', 'ID tidak valid');
        }

        return view('master.detail', [
            'dimensions' => $dimensions,
            'data' => $data,
            'dim_id' => $dim_id,
            'notifications' => $notifications
        ]);
    }

    public function create($dim_id)
    {
        $dimension = Dimension::findOrFail($dim_id);

        return view('master.create', [
            'dimension' => $dimension,
            'dim_id' => $dim_id
        ]);
    }

    // public function store(Request $request, $dim_id)
    // {
    //     try {
    //         // Validasi berdasarkan dimensi
    //         $validationRules = [];
    //         $idField = '';
    //         $idName = '';
    //         $fieldLabels = []; // untuk user-friendly field names

    //         switch ($dim_id) {
    //             case 1: // Line of Business
    //                 $validationRules = [
    //                     'lob_id' => 'required|unique:line_of_businesses,lob_id',
    //                     'line_business' => 'required|unique:line_of_businesses,line_business'
    //                 ];
    //                 $idField = 'lob_id';
    //                 $idName = 'LOB';
    //                 break;

    //             case 2: // Department
    //                 $validationRules = [
    //                     'dpt_id' => 'required|unique:departments,dpt_id',
    //                     'department' => 'required|unique:departments,department',
    //                     'level' => 'required',
    //                     'parent' => 'required',
    //                     'alloc' => 'required'
    //                 ];
    //                 $idField = 'dpt_id';
    //                 $idName = 'Department';
    //                 break;

    //             case 3: // Workcenter
    //                 $validationRules = [
    //                     'wct_id' => 'required|unique:workcenters,wct_id',
    //                     'workcenter' => 'required|unique:workcenters,workcenter'
    //                 ];
    //                 $idField = 'wct_id';
    //                 $idName = 'Workcenter';
    //                 break;

    //             case 4: // Budget Code
    //                 $validationRules = [
    //                     'bdc_id' => 'required|unique:budget_codes,bdc_id',
    //                     'budget_name' => 'required|unique:budget_codes,budget_name'
    //                 ];
    //                 $idField = 'bdc_id';
    //                 $idName = 'Budget Code';
    //                 break;

    //             default:
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Invalid dimension ID'
    //                 ], 400);
    //         }

    //         $validator = Validator::make($request->all(), $validationRules);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validator->errors(),
    //                 'id_field' => $idField,
    //                 'id_name' => $idName
    //             ], 422);
    //         }

    //         // Proses penyimpanan data
    //         switch ($dim_id) {
    //             case 1:
    //                 LineOfBusiness::create([
    //                     'lob_id' => $request->lob_id,
    //                     'line_business' => $request->line_business,
    //                     'status' => 1
    //                 ]);
    //                 break;

    //             case 2:
    //                 Departments::create([
    //                     'dpt_id' => $request->dpt_id,
    //                     'department' => $request->department,
    //                     'level' => $request->level,
    //                     'parent' => $request->parent,
    //                     'alloc' => $request->alloc,
    //                     'status' => 1
    //                 ]);
    //                 break;

    //             case 3:
    //                 Workcenter::create([
    //                     'wct_id' => $request->wct_id,
    //                     'workcenter' => $request->workcenter,
    //                     'status' => 1
    //                 ]);
    //                 break;

    //             case 4:
    //                 BudgetCode::create([
    //                     'bdc_id' => $request->bdc_id,
    //                     'budget_name' => $request->budget_name,
    //                     'status' => 1
    //                 ]);
    //                 break;
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data has been created successfully!'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request, $dim_id)
{
    try {
        $validationRules = [];
        $idField = '';
        $idName = '';
        $fieldLabels = []; // untuk user-friendly field names

        switch ($dim_id) {
            case 1: // Line of Business
                $validationRules = [
                    'lob_id' => 'required|unique:line_of_businesses,lob_id',
                    'line_business' => 'required|unique:line_of_businesses,line_business'
                ];
                $fieldLabels = [
                    'lob_id' => 'ID',
                    'line_business' => 'Line of Business name'
                ];
                $idField = 'lob_id';
                $idName = 'LOB';
                break;

            case 2: // Department
                $validationRules = [
                    'dpt_id' => 'required|unique:departments,dpt_id',
                    'department' => 'required|unique:departments,department',
                    'level' => 'required',
                    'parent' => 'required',
                    'alloc' => 'required'
                ];
                $fieldLabels = [
                    'dpt_id' => 'ID',
                    'department' => 'Department name',
                    'level' => 'Level',
                    'parent' => 'Parent',
                    'alloc' => 'Allocation'
                ];
                $idField = 'dpt_id';
                $idName = 'Department';
                break;

            case 3: // Workcenter
                $validationRules = [
                    'wct_id' => 'required|unique:workcenters,wct_id',
                    'workcenter' => 'required|unique:workcenters,workcenter'
                ];
                $fieldLabels = [
                    'wct_id' => 'ID',
                    'workcenter' => 'Workcenter name'
                ];
                $idField = 'wct_id';
                $idName = 'Workcenter';
                break;

            case 4: // Budget Code
                $validationRules = [
                    'bdc_id' => 'required|unique:budget_codes,bdc_id',
                    'budget_name' => 'required|unique:budget_codes,budget_name'
                ];
                $fieldLabels = [
                    'bdc_id' => 'ID',
                    'budget_name' => 'Budget Code name'
                ];
                $idField = 'bdc_id';
                $idName = 'Budget Code';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid dimension ID'
                ], 400);
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Deteksi field duplicate
            $duplicateFields = [];
            foreach ($validationRules as $field => $rules) {
                if (str_contains($rules, 'unique') && $errors->has($field)) {
                    $duplicateFields[] = $field;
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $errors,
                'duplicate_fields' => $duplicateFields,
                'field_labels' => $fieldLabels,
                'id_field' => $idField,
                'id_name' => $idName
            ], 422);
        }

        // Simpan data
        switch ($dim_id) {
            case 1:
                LineOfBusiness::create([
                    'lob_id' => $request->lob_id,
                    'line_business' => $request->line_business,
                    'status' => 1
                ]);
                break;

            case 2:
                Departments::create([
                    'dpt_id' => $request->dpt_id,
                    'department' => $request->department,
                    'level' => $request->level,
                    'parent' => $request->parent,
                    'alloc' => $request->alloc,
                    'status' => 1
                ]);
                break;

            case 3:
                Workcenter::create([
                    'wct_id' => $request->wct_id,
                    'workcenter' => $request->workcenter,
                    'status' => 1
                ]);
                break;

            case 4:
                BudgetCode::create([
                    'bdc_id' => $request->bdc_id,
                    'budget_name' => $request->budget_name,
                    'status' => 1
                ]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Data has been created successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


    public function updateStatus(Request $request, $dim_id, $id)
    {
        try {
            switch ($dim_id) {
                case 1: // Line of Business
                    $item = LineOfBusiness::findOrFail($id);
                    break;
                case 2: // Departments
                    $item = Departments::findOrFail($id);
                    break;
                case 3: // Workcenter
                    $item = Workcenter::findOrFail($id);
                    break;
                case 4: // Account Budget
                    $item = BudgetCode::findOrFail($id);
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid dimension']);
            }

            $item->status = $request->status;
            $item->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($dim_id, $id)
    {
        $dimension = Dimension::findOrFail($dim_id);
        switch ($dim_id) {
            case 1:
                $item = LineOfBusiness::findOrFail($id);
                break;
            case 2:
                $item = Departments::findOrFail($id);
                break;
            case 3:
                $item = Workcenter::findOrFail($id);
                break;
            case 4:
                $item = BudgetCode::findOrFail($id);
                break;
            default:
                return redirect()->back()->with('error', 'ID tidak valid');
        }

        return view('master.edit', [
            'dimension' => $dimension,
            'item' => $item,
            'dim_id' => $dim_id
        ]);
    }

    public function update(Request $request, $dim_id, $id)
    {
        try {
            $validator = null;
            $item = null;

            switch ($dim_id) {
                case 1:
                    $validator = Validator::make($request->all(), [
                        'lob_id' => 'required|unique:line_of_businesses,lob_id,' . $id . ',id',
                        'line_business' => 'required|unique:line_of_businesses,line_business,' . $id . ',id',
                    ], [
                        'lob_id.unique' => 'The ID is already in use.',
                        'line_business.unique' => 'The Line of Business name is already in use.',
                    ]);
                    break;

                case 2:
                    $validator = Validator::make($request->all(), [
                        'dpt_id' => 'required|unique:departments,dpt_id,' . $id . ',id',
                        'department' => 'required|unique:departments,department,' . $id . ',id',
                        'level' => 'required|integer',
                        'parent' => 'required|integer',
                    ], [
                        'dpt_id.unique' => 'The ID is already in use.',
                        'department.unique' => 'The Department name is already in use.',
                    ]);
                    break;

                case 3:
                    $validator = Validator::make($request->all(), [
                        'wct_id' => 'required|unique:workcenters,wct_id,' . $id . ',id',
                        'workcenter' => 'required|unique:workcenters,workcenter,' . $id . ',id',
                    ], [
                        'wct_id.unique' => 'The ID is already in use.',
                        'workcenter.unique' => 'The Workcenter name is already in use.',
                    ]);
                    break;

                case 4:
                    $validator = Validator::make($request->all(), [
                        'bdc_id' => 'required|unique:budget_codes,bdc_id,' . $id . ',id',
                        'budget_name' => 'required|unique:budget_codes,budget_name,' . $id . ',id',
                    ], [
                        'bdc_id.unique' => 'The ID is already in use.',
                        'budget_name.unique' => 'The Budget Code name is already in use.',
                    ]);
                    break;
            }


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Apply update logic
            switch ($dim_id) {
                case 1:
                    $item = LineOfBusiness::findOrFail($id);
                    $item->update([
                        'lob_id' => $request->lob_id,
                        'line_business' => $request->line_business,
                        'status' => 1,
                    ]);
                    break;

                case 2:
                    $item = Departments::findOrFail($id);
                    $item->update([
                        'dpt_id' => $request->dpt_id,
                        'department' => $request->department,
                        'level' => $request->level,
                        'parent' => $request->parent,
                        'alloc' => $request->alloc,
                        'status' => 1,
                    ]);
                    break;

                case 3:
                    $item = Workcenter::findOrFail($id);
                    $item->update([
                        'wct_id' => $request->wct_id,
                        'workcenter' => $request->workcenter,
                        'status' => 1,
                    ]);
                    break;

                case 4:
                    $item = BudgetCode::findOrFail($id);
                    $item->update([
                        'bdc_id' => $request->bdc_id,
                        'budget_name' => $request->budget_name,
                        'status' => 1,
                    ]);
                    break;
            }

            return response()->json(['success' => true, 'message' => 'Data has been updated successfully!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        } catch (\Exception $e) {
            Log::error('Update error for dim_id ' . $dim_id . ', id ' . $id . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred'], 500);
        }
    }
}
