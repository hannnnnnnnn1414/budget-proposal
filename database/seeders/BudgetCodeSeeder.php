<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('budget_codes')->insert([
            ['bdc_id' => '001', 'budget_name' => 'Raw Material', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '010', 'budget_name' => 'Pension Allow Employee', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '011', 'budget_name' => 'Medical Allow Employee', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '012', 'budget_name' => 'Bpjs Ketenagakerjaan', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '013', 'budget_name' => 'Bpjs Kesehatan', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '014', 'budget_name' => 'Bonus', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '015', 'budget_name' => 'Cuti Besar', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '016', 'budget_name' => 'Honorarium', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '017', 'budget_name' => 'Empben', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '028', 'budget_name' => 'Meal (Food&Drink)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '029', 'budget_name' => 'Catering', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '030', 'budget_name' => 'Resign Allowance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '031', 'budget_name' => 'Sport & Recreation', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '032', 'budget_name' => 'Other Welfare Allowance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '035', 'budget_name' => 'Hospital Allowance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '043', 'budget_name' => 'Employe Welfare Op (Rent)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '045', 'budget_name' => 'Indirect Material', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '046-NR', 'budget_name' => 'Non Routine Factory Supply', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '046-R', 'budget_name' => 'Routine Factory Supply', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '047', 'budget_name' => 'Miscellanous Income', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '048-NR', 'budget_name' => 'Non Routine Consumable Tools', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '048-R', 'budget_name' => 'Routine Consumable Tools', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-NR', 'budget_name' => 'Non Routine Repair Maintenance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-01', 'budget_name' => 'Repair & Maintenance (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-E', 'budget_name' => 'Non Routine Repair Maint Engineering', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-M', 'budget_name' => 'Non Routine Repair Maint Maintenance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-RE', 'budget_name' => 'Routine Repair Maint Engineering', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '050-RM', 'budget_name' => 'Routine Repair Maint Maintenanance', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '054', 'budget_name' => 'Insurance Premium', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '054-01', 'budget_name' => 'Insurance Premium (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '056', 'budget_name' => 'Tax&Public Dues', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '056-01', 'budget_name' => 'Tax&Public Dues (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '057', 'budget_name' => 'Power & Water', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '057-01', 'budget_name' => 'Power & Water (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '058', 'budget_name' => 'Packing Delivery', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '059', 'budget_name' => 'Automobile', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '059-01', 'budget_name' => 'Automobile (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '060', 'budget_name' => 'Travelling', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '060-01', 'budget_name' => 'Travelling (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '062', 'budget_name' => 'Entertainment', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '062-01', 'budget_name' => 'Entertainment (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '063', 'budget_name' => 'Representation', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '063-01', 'budget_name' => 'Representation (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '064', 'budget_name' => 'Training & Education', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '064-01', 'budget_name' => 'Training & Education (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '067', 'budget_name' => 'Technical Development', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '071', 'budget_name' => 'Refund', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '073', 'budget_name' => 'Outsourcing Exp', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '074', 'budget_name' => 'Rent Expense', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '074-01', 'budget_name' => 'Rent Expense (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '075', 'budget_name' => 'Communication', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '076', 'budget_name' => 'Remunation For Bod', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '077', 'budget_name' => 'Advertising & Promotion', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '078', 'budget_name' => 'Marketing Activity', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '079', 'budget_name' => 'Customer Deposit', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '082', 'budget_name' => 'Recruitment', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '082-01', 'budget_name' => 'Recruitment (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '083', 'budget_name' => 'Royalty', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '084', 'budget_name' => 'Office Supply', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '085', 'budget_name' => 'Book & Newspaper', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '086', 'budget_name' => 'Contribution', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '087', 'budget_name' => 'Professional Expense', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '087-01', 'budget_name' => 'Professional Expense (Opex)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '088', 'budget_name' => 'Ppn', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '089', 'budget_name' => 'Association', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '090', 'budget_name' => 'Bank Charges', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '091', 'budget_name' => 'After Sales Service', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '092', 'budget_name' => 'Import Expense', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '093', 'budget_name' => 'Ppn Import', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '094', 'budget_name' => 'Pph Import', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bdc_id' => '095', 'budget_name' => 'Pph 23', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
