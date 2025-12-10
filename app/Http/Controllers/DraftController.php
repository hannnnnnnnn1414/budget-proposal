<?php

namespace App\Http\Controllers;

use App\Models\AfterSalesService;
use App\Models\BookNewspaper;
use App\Models\BusinessDuty;
use App\Models\GeneralExpense;
use App\Models\InsurancePrem;
use App\Models\OfficeOperation;
use App\Models\OperationalSupport;
use App\Models\RepairMaint;
use App\Models\RepresentationExpense;
use App\Models\SupportMaterial;
use App\Models\TrainingEducation;
use App\Models\Utilities;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    public function index($sub_id)
    {
        $officeOps = OfficeOperation::where('sub_id', $sub_id)

            ->get();
        $generalExpenses = GeneralExpense::where('sub_id', $sub_id)

            ->get();
        $repairMaints = RepairMaint::where('sub_id', $sub_id)

            ->get();
        $supportMaterials = SupportMaterial::where('sub_id', $sub_id)

            ->get();
        $insurancePrems = InsurancePrem::where('sub_id', $sub_id)

            ->get();
        $utilities = Utilities::where('sub_id', $sub_id)

            ->get();
        $businessDuties = BusinessDuty::where('sub_id', $sub_id)

            ->get();
        $repExpenses = RepresentationExpense::where('sub_id', $sub_id)

            ->get();
        $trainingEdus = TrainingEducation::where('sub_id', $sub_id)

            ->get();
        $operationalSupps = OperationalSupport::where('sub_id', $sub_id)

            ->get();
        $aftersales = AfterSalesService::where('sub_id', $sub_id)

            ->get();

        $submissions = collect()
            ->merge($officeOps)
            ->merge($generalExpenses)
            ->merge($repairMaints)
            ->merge($supportMaterials)
            ->merge($insurancePrems)
            ->merge($utilities)
            ->merge($businessDuties)
            ->merge($repExpenses)
            ->merge($trainingEdus)
            ->merge($operationalSupps)
            ->merge($aftersales);


        return view('submissions.index', [
            'submissions' => $submissions,
            'sub_id' => $sub_id
        ]);
    }

    // public function indexRepair()
    // {
    //     $drafts = RepairMaint::where('type', 'repair')->get();
    //     return view('drafts.repair', compact('drafts'));
    // }

    public function report($acc_id)
    {
        $reports = collect();

        if (in_array($acc_id, ['SGAADVERT', 'SGACOM', 'SGAOFFICESUP'])) {
            $reports = OfficeOperation::select('sub_id', 'status')
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['SGAASOCIATION', 'SGABCHARGES', 'SGACONTRIBUTION', 'FOHPACKING', 'SGARYLT', 'FOHAUTOMOBILE', 'FOHPROF', 'FOHRENT', 'FOHTAXPUB', 'SGAAUTOMOBILE', 'SGAPROF', 'SGATAXPUB'])) {
            $reports = GeneralExpense::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['SGAMARKT', 'FOHTECHDO', 'FOHRECRUITING', 'SGARECRUITING', 'SGARENT'])) {
            $reports = OperationalSupport::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHTOOLS', 'FOHFS', 'FOHINDMAT', 'FOHREPAIR', 'SGADEPRECIATION'])) {
            $reports = SupportMaterial::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHENTERTAINT', 'FOHREPRESENTATION', 'SGAENTERTAINT', 'SGAREPRESENTATION'])) {
            $reports = RepresentationExpense::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHINSPREM', 'SGAINSURANCE'])) {
            $reports = InsurancePrem::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHPOWER', 'SGAPOWER'])) {
            $reports = Utilities::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHTRAV', 'SGATRAV'])) {
            $reports = BusinessDuty::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif (in_array($acc_id, ['FOHTRAINING', 'SGATRAINING'])) {
            $reports = TrainingEducation::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif ($acc_id === 'SGABOOK') {
            $reports = BookNewspaper::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif ($acc_id === 'SGAREPAIR') {
            $reports = RepairMaint::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        } elseif ($acc_id === 'SGAAFTERSALES') {
            $reports = AfterSalesService::select('sub_id', 'status')
                ->where('status', '!=', 0)
                ->where('acc_id', $acc_id)
                ->groupBy('sub_id', 'status')
                ->get();
        }

        return view('reports.ads-all', compact('reports'));
    }

    public function submit($sub_id)
    {
        $submissions = OfficeOperation::where('sub_id', $sub_id)->get();

        if ($submissions->isNotEmpty()) {
            foreach ($submissions as $submission) {
                $submission->status = 2;
                $submission->save();
            }

            return redirect()->back()->with('success', 'All related submissions have been sent for review.');
        }

        return redirect()->back()->with('error', 'Submission not found.');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $sub_id)
    {
        $submissions = OfficeOperation::where('sub_id', $sub_id)->get();

        if ($submissions->isNotEmpty()) {
            foreach ($submissions as $submission) {
                $submission->status = 0;
                $submission->save();
            }

            return redirect()->back()->with('success', 'Submissions have been reverted to draft.');
        }

        return redirect()->back()->with('error', 'Submission not found.');
    }
}
