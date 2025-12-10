<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AfterSalesService;
use App\Models\Approval;
use App\Models\BookNewspaper;
use App\Models\BudgetCode;
use App\Models\BudgetPlan;
use App\Models\BusinessDuty;
use App\Models\Currency;
use App\Models\Departments;
use App\Models\GeneralExpense;
use App\Models\InsuranceCompany;
use App\Models\InsurancePrem;
use App\Models\Item;
use App\Models\LineOfBusiness;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\Training;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use App\Models\Workcenter;
use Database\Seeders\BudgetCodeSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function index($id)
    {
        $accounts = Account::where('acc_id', $id)->first();
        $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
        $departments = Departments::orderBy('department', 'asc')->get()->pluck('department', 'dpt_id');
        $workcenters = Workcenter::orderBy('workcenter', 'asc')->get()->pluck('workcenter', 'wct_id');
        $budget_codes = BudgetCode::orderBy('budget_name', 'asc')->get()->pluck('budget_name', 'bdc_id');
        $line_business = LineOfBusiness::orderBy('line_business', 'asc')->get()->pluck('line_business', 'lob_id');
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        return view('submissions.index', compact('submissions', 'acc_id', 'notifications'));
    }

    public function create($id)
    {
        $account = Account::where('acc_id', $id)->firstOrFail();
        $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
        $departments = Departments::orderBy('department', 'asc')->where('status', 1)->get()->pluck('department', 'dpt_id');
        $workcenters = Workcenter::orderBy('workcenter', 'asc')->where('status', 1)->get()->pluck('workcenter', 'wct_id');
        $budget_codes = BudgetCode::orderBy('budget_name', 'asc')->where('status', 1)->get()->pluck('budget_name', 'bdc_id');
        $line_business = LineOfBusiness::orderBy('line_business', 'asc')->where('status', 1)->get()->pluck('line_business', 'lob_id');
        $insurance_prems = InsuranceCompany::orderBy('company', 'asc')->where('status', 1)->get()->pluck('company', 'ins_id');
        $currencies = Currency::where('status', 1)->get()->mapWithKeys(function ($currency) {
            return [$currency->cur_id => ['currency' => $currency->currency, 'nominal' => $currency->nominal]];
        });
        $notificationController = new NotificationController();
        $notifications = $notificationController->getNotifications();

        if (!$account) {
            return response()->json(['message' => 'account not found'], 404);
        }

        switch ($account->acc_id) {
            case 'SGAADVERT':
                $viewName = 'accounts.ads';
                break;
            case 'SGAAFTERSALES':
                $viewName = 'accounts.aftersales';
                break;
            case 'SGAASSOCIATION':
                $viewName = 'accounts.assoc';
                break;
            case 'SGABCHARGES':
                $viewName = 'accounts.bank';
                break;
            case 'SGABOOK':
                $viewName = 'accounts.book';
                break;
            case 'SGACOM':
                $viewName = 'accounts.comm';
                break;
            case 'SGACONTRIBUTION':
                $viewName = 'accounts.contrib';
                break;
            case 'FOHTOOLS':
                $viewName = 'accounts.tools';
                break;
            case 'FOHFS':
                $viewName = 'accounts.supply';
                break;
            case 'FOHINDMAT':
                $viewName = 'accounts.imaterial';
                break;
            case 'SGAMARKT':
                $viewName = 'accounts.marketing';
                break;
            case 'SGAOFFICESUP':
                $viewName = 'accounts.supply';
                break;
            case 'FOHPACKING':
                $viewName = 'accounts.packing';
                break;
            case 'SGARYLT':
                $viewName = 'accounts.royalty';
                break;
            case 'FOHTECHDO':
                $viewName = 'accounts.techdev';
                break;
            case 'FOHAUTOMOBILE':
                $viewName = 'accounts.automobile';
                break;
            case 'FOHTRAV':
                $viewName = 'accounts.business';
                break;
            case 'FOHENTERTAINT':
                $viewName = 'accounts.entertain';
                break;
            case 'FOHINSPREM':
                $viewName = 'accounts.insurance';
                break;
            case 'FOHPROF':
                $viewName = 'accounts.profee';
                break;
            case 'FOHRECRUITING':
                $viewName = 'accounts.recruitment';
                break;
            case 'FOHREPAIR':
                $viewName = 'accounts.repair';
                break;
            case 'SGADEPRECIATION':
                $viewName = 'accounts.depreciation';
                break;
            case 'FOHRENT':
                $viewName = 'accounts.rent';
                break;
            case 'FOHREPRESENTATION':
                $viewName = 'accounts.representation';
                break;
            case 'FOHTRAINING':
                $viewName = 'accounts.training';
                break;
            case 'FOHTAXPUB':
                $viewName = 'accounts.tax';
                break;
            case 'FOHPOWER':
                $viewName = 'accounts.utilities';
                break;
            case 'SGAAUTOMOBILE':
                $viewName = 'accounts.automobile';
                break;
            case 'SGATRAV':
                $viewName = 'accounts.business';
                break;
            case 'SGAENTERTAINT':
                $viewName = 'accounts.entertain';
                break;
            case 'SGAINSURANCE':
                $viewName = 'accounts.insurance';
                break;
            case 'SGAPROF':
                $viewName = 'accounts.profee';
                break;
            case 'SGARECRUITING':
                $viewName = 'accounts.recruitment';
                break;
            case 'SGAREPAIR':
                $viewName = 'accounts.repair';
                break;
            case 'SGARENT':
                $viewName = 'accounts.rent';
                break;
            case 'SGAREPRESENTATION':
                $viewName = 'accounts.representation';
                break;
            case 'SGATRAINING':
                $viewName = 'accounts.training';
                break;
            case 'SGATAXPUB':
                $viewName = 'accounts.tax';
                break;
            case 'SGAPOWER':
                $viewName = 'accounts.utilities';
                break;
            case 'CAPEX':
                $viewName = 'accounts.exp';
                break;
            case 'SGAOUTSOURCING':
                $viewName = 'accounts.outsourcing';
                break;
            case 'PURCHASEMATERIAL':
                $viewName = 'accounts.purchasematerial';
                break;
            case 'FOHEMPLOYCOMPDL':
                $viewName = 'accounts.employee';
                break;
            case 'FOHEMPLOYCOMPIL':
                $viewName = 'accounts.employee';
                break;
            case 'SGAEMPLOYCOMP':
                $viewName = 'accounts.employee';
                break;
            default:
                return response()->json(['message' => 'No account preview available for this selection'], 404);
        }

        return view($viewName, [
            'account' => $account,
            'items' => $items,
            'departments' => $departments,
            'workcenters' => $workcenters,
            'line_business' => $line_business,
            'budget_codes' => $budget_codes,
            'insurance_prems' => $insurance_prems,
            'notifications' => $notifications,
            'acc_id' => $id,
            'currencies' => $currencies
        ]);
    }

    public function getItemName(Request $request)
    {
        $itm_id = $request->input('itm_id');
        $item = Item::where('itm_id', $itm_id)->first();

        if ($item) {
            return response()->json(['item' => $item]);
        } else {
            return response()->json(['item' => null], 404);
        }
    }

    public function getCurrencies(Request $request)
    {
        $currencies = Currency::where('status', 1)->get(['cur_id', 'currency', 'nominal']);
        return response()->json(['currencies' => $currencies]);
    }

    public function addTempData(Request $request)
    {
        $accId = $request->input('acc_id');
        $data = ['acc_id' => $accId];
        session(['purpose' => $request->input('purpose')]);
        $data['month'] = $request->input('month');

        $fieldRules = [
            'SGAADVERT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAAFTERSALES' => ['itm_id', 'customer', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAASSOCIATION' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGABCHARGES' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGABOOK' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGACOM' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGACONTRIBUTION' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHTOOLS' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'FOHFS' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'FOHINDMAT' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'SGAMARKT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAOFFICESUP' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHPACKING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGARYLT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHTECHDO' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHAUTOMOBILE' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHTRAV' => ['trip_propose', 'destination', 'days', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHENTERTAINT' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHINSPREM' => ['description', 'ins_id', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHPROF' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHRECRUITING' => ['itm_id', 'description', 'position', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHREPAIR' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'SGADEPRECIATION' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'FOHRENT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHREPRESENTATION' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGATRAINING' => ['participant', 'jenis_training', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHTAXPUB' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHPOWER' => ['itm_id', 'kwh', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id'],
            'SGAAUTOMOBILE' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGATRAV' => ['trip_propose', 'destination', 'days', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAENTERTAINT' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAINSURANCE' => ['description', 'ins_id', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAPROF' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGARECRUITING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAREPAIR' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'SGARENT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAREPRESENTATION' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'FOHTRAINING' => ['participant', 'jenis_training', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGATAXPUB' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAPOWER' => ['itm_id', 'kwh', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id'],
            'CAPEX' => ['itm_id', 'asset_class', 'prioritas', 'alasan', 'keterangan', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'SGAOUTSOURCING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'],
            'PURCHASEMATERIAL' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id', 'business_partner'],
            'FOHEMPLOYCOMPDL' => ['itm_id', 'ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'FOHEMPLOYCOMPIL' => ['itm_id', 'ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
            'SGAEMPLOYCOMP' => ['itm_id', 'ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'],
        ];

        if (!array_key_exists($accId, $fieldRules)) {
            return redirect()->back()->with('error', 'Invalid account ID');
        }

        foreach ($fieldRules[$accId] as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        if ($request->has('quantity') && in_array($accId, ['FOHTRAINING', 'SGATRAINING'])) {
            $data['quantity'] = $request->input('quantity');
        }

        if ($request->has('cur_id')) {
            $currency = Currency::where('cur_id', $request->input('cur_id'))->first();

            if ($currency) {
                $data['cur_id'] = $currency->cur_id;
                $data['currency'] = $currency->currency;

                if ($currency->currency !== 'IDR') {
                    $data['original_price'] = $request->input('price');
                    $data['price'] = $request->input('price') * $currency->nominal;
                } else {
                    $data['price'] = $request->input('price');
                }

                $data['amount'] = $data['price'];
            } else {
                return redirect()->back()->with('error', 'Invalid currency selected');
            }
        } else {
            return redirect()->back()->with('error', 'Currency is required');
        }

        $tempData = session()->get('temp_data', []);
        $tempData[] = $data;
        session()->put('temp_data', $tempData);

        return redirect()->back()->with('success', 'Data added!');
    }

    public function removeTempData($index)
    {
        $tempData = session()->get('temp_data', []);
        unset($tempData[$index]);
        session()->put('temp_data', $tempData);

        return redirect()->back()->with('success', 'Data Removed!');
    }

    public function uploadPdf(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:2048',
        ]);

        $file = $request->file('pdf_file');
        $fileName = $file->getClientOriginalName();
        $fileContent = file_get_contents($file->getRealPath());
        $fileBase64 = base64_encode($fileContent);

        $pdfData = [
            'name' => $fileName,
            'content' => $fileBase64
        ];

        $pdfs = session()->get('pdf_attachment', []);
        $pdfs[] = $pdfData;
        session()->put('pdf_attachment', $pdfs);

        return response()->json([
            'success' => true,
            'pdfs' => $pdfs
        ]);
    }

    public function removePdf(Request $request)
    {
        $index = $request->input('index');
        $pdfs = session()->get('pdf_attachment', []);

        if (isset($pdfs[$index])) {
            unset($pdfs[$index]);
            $pdfs = array_values($pdfs);
            session()->put('pdf_attachment', $pdfs);
        }

        return response()->json([
            'success' => true,
            'pdfs' => $pdfs
        ]);
    }

    public function store(Request $request)
    {
        $tempData = session()->get('temp_data', []);
        $pdfAttachments = session()->get('pdf_attachment', []);
        $purpose = session('purpose');
        $deptId = Auth::user()->dept;
        $newSubId = $this->generateSubId($request->input('acc_id'));
        if ($request->input('acc_id') === 'CAPEX' && empty($pdfAttachments)) {
            return redirect()->back()->with('error', 'A PDF attachment is required for CAPEX submissions.');
        }
        foreach ($tempData as $data) {
            $genexp = ['SGAASSOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGABOOK', 'SGAREPAIR', 'SGAOUTSOURCING'];
            $employeeComp = ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'];
            $suppmat = ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'];
            $repexp = ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'];
            $insurance = ['FOHINSPREM', 'SGAINSURANCE'];
            $utilities = ['FOHPOWER', 'SGAPOWER'];
            $business = ['FOHTRAV', 'SGATRAV'];
            $training = ['FOHTRAINING', 'SGATRAINING'];
            $data['dpt_id'] = $deptId;
            if (in_array($request->input('acc_id'), $genexp)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'description'   => $data['description'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                    'business_partner' => $data['business_partner'] ?? null,
                ]);
            } elseif (in_array($request->input('acc_id'), $employeeComp)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'] ?? null,
                    'ledger_account' => $data['ledger_account'],
                    'ledger_account_description' => $data['ledger_account_description'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'lob_id'        => $data['lob_id'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif ($request->input('acc_id') === 'PURCHASEMATERIAL') {
                BudgetPlan::create([
                    'sub_id' => $newSubId,
                    'purpose' => $purpose,
                    'acc_id' => $request->input('acc_id'),
                    'itm_id' => $data['itm_id'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'amount' => $data['amount'],
                    'wct_id' => $data['wct_id'],
                    'dpt_id' => $data['dpt_id'],
                    'month' => $data['month'],
                    'lob_id' => $data['lob_id'] ?? null,
                    'bdc_id' => $data['bdc_id'] ?? null,
                    'business_partner' => $data['business_partner'] ?? null,
                    'status' => 1,
                ]);
            } elseif ($request->input('acc_id') === 'SGAAFTERSALES') {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'customer'      => $data['customer'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $suppmat)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'description'   => $data['description'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'lob_id'        => $data['lob_id'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $repexp)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'description'   => $data['description'],
                    'beneficiary'   => $data['beneficiary'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $insurance)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'description'   => $data['description'],
                    'ins_id'        => $data['ins_id'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $utilities)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'kwh'           => $data['kwh'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'lob_id'        => $data['lob_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $business)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'trip_propose'  => $data['trip_propose'],
                    'destination'   => $data['destination'],
                    'days'          => $data['days'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'status'        => 1,
                ]);
            } elseif (in_array($request->input('acc_id'), $training)) {
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'participant'   => $data['participant'],
                    'jenis_training' => $data['jenis_training'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                ]);
            } elseif ($request->input('acc_id') === 'CAPEX') {
                Log::info('Saving CAPEX data:', $data);
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'],
                    'asset_class'   => $data['asset_class'],
                    'prioritas'     => $data['prioritas'],
                    'alasan'        => $data['alasan'],
                    'keterangan'    => $data['keterangan'],
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'quantity'      => $data['quantity'] ?? null,
                    'bdc_id'        => $data['bdc_id'] ?? null,
                    'status'        => 1,
                    'pdf_attachment' => json_encode($pdfAttachments),
                ]);
            }

            Approval::create([
                'sub_id' => $newSubId,
                'approve_by' => Auth::user()->npk,
                'status' => 1
            ]);
        }

        session()->forget(['temp_data', 'purpose', 'pdf_attachment']);
        return redirect(route('submissions.index'))->with('success', 'Submission added successfully');
    }

    private function generateSubId($accId)
    {
        $prefixes = [
            'SGAADVERT' => 'ADP',
            'SGAAFTERSALES' => 'AFS',
            'SGAASSOCIATION' => 'ASC',
            'SGABCHARGES' => 'BKC',
            'SGABOOK' => 'BKN',
            'SGACOM' => 'COM',
            'SGACONTRIBUTION' => 'CTR',
            'FOHTOOLS' => 'CTL',
            'FOHFS' => 'FSU',
            'FOHINDMAT' => 'IDM',
            'SGAMARKT' => 'MKT',
            'SGAOFFICESUP' => 'OFS',
            'FOHPACKING' => 'PKD',
            'SGARYLT' => 'RYL',
            'FOHTECHDO' => 'TCD',
            'FOHAUTOMOBILE' => 'AUTF',
            'FOHTRAV' => 'BSDF',
            'FOHENTERTAINT' => 'ENTF',
            'FOHINSPREM' => 'INSF',
            'FOHPROF' => 'PRFF',
            'FOHRECRUITING' => 'RECF',
            'FOHREPAIR' => 'RPMF',
            'SGADEPRECIATION' => 'DPR',
            'FOHRENT' => 'REXF',
            'FOHREPRESENTATION' => 'REPF',
            'FOHTRAINING' => 'TEDF',
            'FOHTAXPUB' => 'TAXF',
            'FOHPOWER' => 'UTLF',
            'SGAAUTOMOBILE' => 'AUTO',
            'SGATRAV' => 'BSDO',
            'SGAENTERTAINT' => 'ENTO',
            'SGAINSURANCE' => 'INSO',
            'SGAPROF' => 'PRFO',
            'SGARECRUITING' => 'RECO',
            'SGAREPAIR' => 'RPMO',
            'SGARENT' => 'REXO',
            'SGAREPRESENTATION' => 'REPO',
            'SGATRAINING' => 'TEDO',
            'SGATAXPUB' => 'TAXO',
            'SGAPOWER' => 'UTLO',
            'CAPEX' => 'EXP',
            'SGAOUTSOURCING' => 'OUT',
            'PURCHASEMATERIAL' => 'PUR',
            'FOHEMPLOYCOMPDL' => 'ECDL',
            'FOHEMPLOYCOMPIL' => 'ECIL',
            'SGAEMPLOYCOMP' => 'ECSG',
        ];

        if (!array_key_exists($accId, $prefixes)) {
            throw new \Exception('Invalid acc_id');
        }

        $prefix = $prefixes[$accId];
        $lastId = 0;

        $lastSubmission = null;
        if (in_array($prefix, ['ADP', 'COM', 'OFS', 'ASC', 'BKC', 'CTR', 'PKD', 'RYL', 'TCD', 'AUTF', 'PRFF', 'REXF', 'RPMO', 'TAXF', 'AUTO', 'PRFO', 'TAXO', 'MKT', 'TCD', 'RECF', 'RECO', 'REXO', 'BKN', 'OUT'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif ($prefix === 'AFS') {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['CTL', 'FSU', 'IDM', 'RPMF', 'ECDL', 'ECIL', 'ECSG'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['CTL', 'FSU', 'IDM', 'RPMF'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['ENTF', 'REPF', 'ENTO', 'REPO'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['INSF', 'INSO'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['UTLF', 'UTLO'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['BSDF', 'BSDO'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['TEDF', 'TEDO'])) {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif ($prefix === 'EXP') {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        }

        if ($lastSubmission) {
            $lastId = intval(substr($lastSubmission->sub_id, strlen($prefix)));
        }

        return $prefix . str_pad($lastId + 1, 7, '0', STR_PAD_LEFT);
    }

    public function cancel(Request $request)
    {
        $request->session()->forget(['temp_data', 'purpose']);

        return redirect()->route('submissions.index')->with('success', 'Submission canceled successfully.');
    }

    public function downloadDocuments($sub_id)
    {
        $submission = BudgetPlan::where('sub_id', $sub_id)->first();

        if (!$submission || empty($submission->pdf_attachment)) {
            abort(404, 'No documents found');
        }

        $pdfAttachments = json_decode($submission->pdf_attachment, true);

        if (empty($pdfAttachments)) {
            abort(404, 'No documents found');
        }

        $pdf = $pdfAttachments[0];
        return response()->make(
            base64_decode($pdf['content']),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdf['name'] . '"'
            ]
        );
    }

    public function clearSession(Request $request)
    {
        $request->session()->forget(['temp_data', 'purpose']);

        return redirect()->back()->with('success', 'Sesi telah dibersihkan.');
    }
}
