<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            ['acc_id' => 'FOHINDMAT', 'account' => 'SUPPLEMENT MAT-INDIRECT USED', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHFS', 'account' => 'SUPPLEMENT MAT-FACTORY SUPP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHTOOLS', 'account' => 'CONSUMABLE TOOLS', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHEMPLOYCOMPDL', 'account' => 'EMPLOYEE CONPENSATION DIRECT LABOR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHOVERTIMEPERMNDL', 'account' => 'OVERTIME (SHIFT ALLOWANCE, INCENTIVE)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHOVERTIMENONPERMNDL', 'account' => 'OVERTIME (SHIFT ALLOWANCE, INCENTIVE) Contract', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHEMPLOYCOMPIL', 'account' => 'EMPLOYEE COMPENSATION INDIRECT LABOR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHOVERTIMEPERMNIL', 'account' => 'OVERTIME (SHIFT ALLOWANCE, INCENTIVE)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHOVERTIMENONPERMNIL', 'account' => 'OVERTIME (SHIFT ALLOWANCE, INCENTIVE) Contract', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHREPAIR', 'account' => 'REPAIR & MAINTENANCE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHINSPREM', 'account' => 'INSURANCE PREMIUM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHTAXPUB', 'account' => 'TAX AND PUBLIC DUES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHPROF', 'account' => 'PROFESSIONAL FEE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHPOWER', 'account' => 'POWER AND WATER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHPACKING', 'account' => 'PACKING AND DELIVERY CHARGES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHAUTOMOBILE', 'account' => 'AUTOMOBILE EXP FOR COMERC CAR', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHRENT', 'account' => 'AUTOMOBILE EXP FOR RENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHTRAV', 'account' => 'TRAVELLING EXP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHENTERTAINT', 'account' => 'ENTERTAINMENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHREPRESENTATION', 'account' => 'REPRESENTATION', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHTRAINING', 'account' => 'TRAINING & EDUCATION EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHTECHDO', 'account' => 'TECHN DEVELOP AND TRIAL EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHRECRUITING', 'account' => 'RECRUITMENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'FOHDEFECTCO', 'account' => 'DEFECTIVE COST', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

            ['acc_id' => 'SGAEMPLOYCOMP', 'account' => 'EMPLOYEE COMPENSATION', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAOVERTIME', 'account' => 'OVERTIME (SHIFT ALLOWANCE, INCENTIVE)', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAREPAIR', 'account' => 'REPAIR & MAINTENANCE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAINSURANCE', 'account' => 'INSURANCE PREMIUM', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGATAXPUB', 'account' => 'TAX AND PUBLIC DUES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGARENT', 'account' => 'AUTOMOBILE RENT', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAAUTOMOBILE', 'account' => 'AUTOMOBILE EXP. SEDAN', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAPOWER', 'account' => 'POWER AND WATER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGABCHARGES', 'account' => 'BANK CHARGES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGARYLT', 'account' => 'ROYALTY', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAOFFICESUP', 'account' => 'OFFICE SUPPLIES', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGABOOK', 'account' => 'BOOK & NEWSPAPER', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGACOM', 'account' => 'COMMUNICATION EXP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGATRAV', 'account' => 'TRAVELLING EXP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAPROF', 'account' => 'PROFESSIONAL EXP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAENTERTAINT', 'account' => 'ENTERTAINMENT EXP.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAREPRESENTATION', 'account' => 'REPRESENTATION', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGACONTRIBUTION', 'account' => 'CONTRIBUTION EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAASSOCIATION', 'account' => 'ASSOCIATION EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAADVERT', 'account' => 'ADVERT AND SALES PROMOTION', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGATRAINING', 'account' => 'TRAINING & EDUCATION EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGARECRUITING', 'account' => 'RECRUITING EXP', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAMARKT', 'account' => 'MARKETING ACTIVITY', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['acc_id' => 'SGAAFTERSALES', 'account' => 'AFTER SALES SERVICE', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
