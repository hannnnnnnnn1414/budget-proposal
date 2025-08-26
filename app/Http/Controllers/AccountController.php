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
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
{
    $account = Account::where('acc_id', $id)->firstOrFail();
    $items = Item::orderBy('item', 'asc')->get()->pluck('item', 'itm_id');
    $departments = Departments::orderBy('department', 'asc')->where('status', 1)->get()->pluck('department', 'dpt_id');
    $workcenters = Workcenter::orderBy('workcenter', 'asc')->where('status', 1)->get()->pluck('workcenter', 'wct_id');
    $budget_codes = BudgetCode::orderBy('budget_name', 'asc')->where('status', 1)->get()->pluck('budget_name', 'bdc_id');
    $line_business = LineOfBusiness::orderBy('line_business', 'asc')->where('status', 1)->get()->pluck('line_business', 'lob_id');
    $insurance_prems = InsuranceCompany::orderBy('company', 'asc')->where('status', 1)->get()->pluck('company', 'ins_id');
    $currencies = Currency::where('status', 1)->get()->mapWithKeys(function ($currency) { // [MODIFIKASI] Ambil data lengkap termasuk nominal
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
    // //     /**
    //      * Store a newly created resource in storage.
    //      */
    public function addTempData(Request $request)
{
    $accId = $request->input('acc_id');
    $data = ['acc_id' => $accId];
    session(['purpose' => $request->input('purpose')]);
    $data['month'] = $request->input('month');

    $fieldRules = [
        'SGAADVERT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAAFTERSALES' => ['itm_id', 'customer', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAASSOCIATION' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGABCHARGES' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGABOOK' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGACOM' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGACONTRIBUTION' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHTOOLS' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id','bdc_id'], // [MODIFIKASI] Gunakan itm_id
        'FOHFS' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id','bdc_id'], // [MODIFIKASI] Gunakan itm_id
        'FOHINDMAT' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id','bdc_id'], // [MODIFIKASI] Gunakan itm_id
        'SGAMARKT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAOFFICESUP' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHPACKING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGARYLT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHTECHDO' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHAUTOMOBILE' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHTRAV' => ['trip_propose', 'destination', 'days', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Ganti itm_id, description dengan trip_propose, destination, hapus bdc_id
        'FOHENTERTAINT' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHINSPREM' => ['description', 'ins_id', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Tidak perlu itm_id untuk FOHINSPREM
        'FOHPROF' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHRECRUITING' => ['itm_id', 'description','position', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHREPAIR' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id','bdc_id'], // [MODIFIKASI] Gunakan itm_id
        'FOHRENT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHREPRESENTATION' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGATRAINING' => ['participant', 'jenis_training', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Tidak perlu itm_id untuk SGATRAINING
        'FOHTAXPUB' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHPOWER' => ['itm_id', 'kwh', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id'], // [MODIFIKASI] Tambah lob_id, hapus quantity dan bdc_id
        'SGAAUTOMOBILE' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGATRAV' => ['trip_propose', 'destination', 'days', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAENTERTAINT' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAINSURANCE' => ['description', 'ins_id', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Tidak perlu itm_id untuk SGAINSURANCE
        'SGAPROF' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGARECRUITING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAREPAIR' => ['itm_id', 'description', 'unit', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id','bdc_id'], // [MODIFIKASI] Gunakan itm_id
        'SGARENT' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAREPRESENTATION' => ['itm_id', 'description', 'beneficiary', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'FOHTRAINING' => ['participant', 'jenis_training', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Tidak perlu itm_id untuk FOHTRAINING
        'SGATAXPUB' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAPOWER' => ['itm_id', 'kwh', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id'], // [MODIFIKASI] Tambah lob_id, hapus quantity dan bdc_id
        'CAPEX' => ['itm_id', 'asset_class', 'prioritas', 'alasan', 'keterangan', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Gunakan itm_id
        'SGAOUTSOURCING' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month'], // [MODIFIKASI] Tambahkan aturan field untuk SGAOUTSOURCING
        'PURCHASEMATERIAL' => ['itm_id', 'description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id', 'business_partner'], // [MODIFIKASI] Tambahkan rnr dan business_partner untuk PURCHASEMATERIAL
        'FOHEMPLOYCOMPDL' => ['itm_id','ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'], // [MODIFIKASI] Tambahkan aturan field untuk FOHEMPLOYCOMPDL
        'FOHEMPLOYCOMPIL' => ['itm_id','ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'], // [MODIFIKASI] Tambahkan aturan field untuk FOHEMPLOYCOMPIL
        'SGAEMPLOYCOMP' => ['itm_id','ledger_account', 'ledger_account_description', 'price', 'amount', 'wct_id', 'dpt_id', 'month', 'lob_id', 'bdc_id'], // [MODIFIKASI] Tambahkan aturan field untuk SGAEMPLOYCOMP
    ];

    // Pastikan acc_id valid
    if (!array_key_exists($accId, $fieldRules)) {
        return redirect()->back()->with('error', 'Invalid account ID');
    }

    // Ambil hanya field yang diperlukan
    foreach ($fieldRules[$accId] as $field) {
        if ($request->has($field)) {
            $data[$field] = $request->input($field);
        }
    }

    // [MODIFIKASI] Map nilai rnr ke bdc_id untuk PURCHASEMATERIAL
    // if ($accId === 'PURCHASEMATERIAL' && $request->has('rnr')) {
    //     $data['bdc_id'] = $request->input('rnr'); // Simpan nilai R/NR ke bdc_id
    // }

    // [MODIFIKASI] Set quantity dan bdc_id ke NULL karena tidak ada di form
    // $data['quantity'] = null;
    // $data['bdc_id'] = null;
    if ($request->has('quantity') && in_array($accId, ['FOHTRAINING', 'SGATRAINING'])) {
        $data['quantity'] = $request->input('quantity'); // [MODIFIKASI] Simpan quantity
    }

    // [MODIFIKASI] Hapus logika penanganan item input karena field Item Type dihapus
    // $noItemId = ['FOHINSPREM', 'SGAINSURANCE', 'SGATRAINING', 'FOHTRAINING'];
    // // Handle item input (manual or from database)
    // if (!in_array($accId, $noItemId)) {
    //     if ($request->input('input_type') === 'manual' && $request->has('manual_item')) {
    //         $data['itm_id'] = $request->input('manual_item'); // Gunakan manual_item sebagai itm_id
    //     } elseif ($request->input('input_type') === 'select' && $request->has('itm_id')) {
    //         $data['itm_id'] = $request->input('itm_id'); // Gunakan ID item dari dropdown
    //     } else {
    //         return redirect()->back()->with('error', 'Invalid item input');
    //     }
    // }

    if ($request->has('cur_id')) {
        $currency = Currency::where('cur_id', $request->input('cur_id'))->first();
        
        if ($currency) {
            // Simpan currency yang dipilih
            $data['cur_id'] = $currency->cur_id;
            $data['currency'] = $currency->currency;
            
            // Jika mata uang bukan IDR, simpan harga asli dan hitung harga dalam IDR
            if ($currency->currency !== 'IDR') {
                $data['original_price'] = $request->input('price'); // Harga dalam mata uang asing
                $data['price'] = $request->input('price') * $currency->nominal; // Konversi ke IDR
            } else {
                $data['price'] = $request->input('price'); // Harga sudah dalam IDR
            }
            
            // [MODIFIKASI] Amount hanya berdasarkan price karena quantity dihapus dari form
            $data['amount'] = $data['price'];
        } else {
            return redirect()->back()->with('error', 'Invalid currency selected');
        }
    } else {
        return redirect()->back()->with('error', 'Currency is required');
    }

    // Simpan ke session
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
        // 'pdf_description' => 'nullable|string|max:255'
    ]);

    $file = $request->file('pdf_file');
    $fileName = $file->getClientOriginalName();
    $fileContent = file_get_contents($file->getRealPath());
    $fileBase64 = base64_encode($fileContent);

    $pdfData = [
        'name' => $fileName,
        // 'description' => $request->input('pdf_description', ''),
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
        $pdfs = array_values($pdfs); // Reindex array
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
        $genexp = ['SGAASSOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB', 'SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT', 'SGAADVERT', 'SGACOM', 'SGAOFFICESUP', 'SGABOOK', 'SGAREPAIR', 'SGAOUTSOURCING']; // [MODIFIKASI] Tambahkan PURCHASEMATERIAL ke genexp
        $employeeComp = ['FOHEMPLOYCOMPDL', 'FOHEMPLOYCOMPIL', 'SGAEMPLOYCOMP'];
        $suppmat = ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR'];
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
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'description'   => $data['description'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
                'business_partner' => $data['business_partner'] ?? null, // [MODIFIKASI] Tambahkan business_partner
            ]);
        } elseif (in_array($request->input('acc_id'), $employeeComp)) { // [MODIFIKASI] Tambahkan kondisi untuk employee compensation
                BudgetPlan::create([
                    'sub_id'        => $newSubId,
                    'purpose'       => $purpose,
                    'acc_id'        => $request->input('acc_id'),
                    'itm_id'        => $data['itm_id'] ?? null,
                    'ledger_account' => $data['ledger_account'], // [MODIFIKASI] Simpan ke kolom ledger_account
                    'ledger_account_description' => $data['ledger_account_description'], // [MODIFIKASI] Simpan ke kolom ledger_account_description
                    'price'         => $data['price'],
                    'amount'        => $data['amount'],
                    'wct_id'        => $data['wct_id'],
                    'dpt_id'        => $data['dpt_id'],
                    'month'         => $data['month'],
                    'lob_id'        => $data['lob_id'] ?? null, // [MODIFIKASI] Tambahkan lob_id
                    'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Tambahkan bdc_id
                    'status'        => 1,
                ]);
        } elseif ($request->input('acc_id') === 'PURCHASEMATERIAL') { // [MODIFIKASI] Tambah kondisi untuk PURCHASEMATERIAL
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
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'customer'      => $data['customer'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
            ]);
        } elseif (in_array($request->input('acc_id'), $suppmat)) {
            BudgetPlan::create([
                'sub_id'        => $newSubId,
                'purpose'       => $purpose,
                'acc_id'        => $request->input('acc_id'),
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'description'   => $data['description'],
                // 'unit'          => $data['unit'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'lob_id'        => $data['lob_id'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id']?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
            ]);
        } elseif (in_array($request->input('acc_id'), $repexp)) {
            BudgetPlan::create([
                'sub_id'        => $newSubId,
                'purpose'       => $purpose,
                'acc_id'        => $request->input('acc_id'),
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'description'   => $data['description'],
                'beneficiary'   => $data['beneficiary'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
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
                // 'quantity'      => null, // [MODIFIKASI] Set quantity ke NULL
                // 'bdc_id'        => null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
            ]);
        } elseif (in_array($request->input('acc_id'), $utilities)) {
            BudgetPlan::create([
                'sub_id'        => $newSubId,
                'purpose'       => $purpose,
                'acc_id'        => $request->input('acc_id'),
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'kwh'           => $data['kwh'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'lob_id'        => $data['lob_id'] ?? null, // [MODIFIKASI] Tambah lob_id
                'status'        => 1,
            ]);
        } elseif (in_array($request->input('acc_id'), $business)) {
            BudgetPlan::create([
                'sub_id'        => $newSubId,
                'purpose'       => $purpose,
                'acc_id'        => $request->input('acc_id'),
                'trip_propose'  => $data['trip_propose'], // [MODIFIKASI] Ganti itm_id dengan trip_propose
                'destination'   => $data['destination'], // [MODIFIKASI] Ganti description dengan destination
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
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
            ]);
        } elseif ($request->input('acc_id') === 'CAPEX') {
            Log::info('Saving CAPEX data:', $data);
            BudgetPlan::create([
                'sub_id'        => $newSubId,
                'purpose'       => $purpose,
                'acc_id'        => $request->input('acc_id'),
                'itm_id'        => $data['itm_id'], // [MODIFIKASI] Gunakan itm_id
                'asset_class'   => $data['asset_class'],
                'prioritas'     => $data['prioritas'],
                'alasan'        => $data['alasan'],
                'keterangan'    => $data['keterangan'],
                'price'         => $data['price'],
                'amount'        => $data['amount'],
                'wct_id'        => $data['wct_id'],
                'dpt_id'        => $data['dpt_id'],
                'month'         => $data['month'],
                'quantity'      => $data['quantity'] ?? null, // [MODIFIKASI] Set quantity ke NULL
                'bdc_id'        => $data['bdc_id'] ?? null, // [MODIFIKASI] Set bdc_id ke NULL
                'status'        => 1,
                'pdf_attachment' => json_encode($pdfAttachments),
            ]);
        }

        Approval::create([
            'sub_id' => $newSubId,
            'approve_by' => Auth::user()->npk, // atau null jika belum diapprove
            'status' => 1 // Status awal
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

        // Generate the new sub_id
        $lastSubmission = null;
        if (in_array($prefix, ['ADP', 'COM', 'OFS', 'ASC', 'BKC', 'CTR', 'PKD', 'RYL', 'TCD', 'AUTF', 'PRFF', 'REXF', 'RPMO', 'TAXF', 'AUTO', 'PRFO', 'TAXO', 'MKT', 'TCD', 'RECF', 'RECO', 'REXO', 'BKN', 'OUT'])) { // [MODIFIKASI] Tambahkan OUT ke daftar prefix
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif ($prefix === 'AFS') {
            $lastSubmission = BudgetPlan::where('sub_id', 'like', $prefix . '%')
                ->orderBy('sub_id', 'desc')
                ->first();
        } elseif (in_array($prefix, ['CTL', 'FSU', 'IDM', 'RPMF', 'ECDL', 'ECIL', 'ECSG'])) { // [MODIFIKASI] Tambahkan ECDL, ECIL, ECSG
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
        // Clear session data
        $request->session()->forget(['temp_data', 'purpose']);

        // Redirect to the submissions index or another desired page
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

    // Always download the first PDF file directly
    $pdf = $pdfAttachments[0]; // Take the first PDF
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
        // Hapus data sesi yang relevan
        $request->session()->forget(['temp_data', 'purpose']);

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Sesi telah dibersihkan.');
    }

}