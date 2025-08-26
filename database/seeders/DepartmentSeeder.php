<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            ['dpt_id' => '1111', 'department' => 'PROD1 (GA)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1112', 'department' => 'PROD1 (MTN)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1113', 'department' => 'PROD1 (PCE)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1114', 'department' => 'PROD1 (PPC)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1115', 'department' => 'PROD1 (WH JT)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1116', 'department' => 'PROD1 (HRD)', 'level' => 0, 'parent' => 1110, 'alloc' => 'GEN', 'status' => 1],

            ['dpt_id' => '1131', 'department' => 'PROD2 (GA)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1132', 'department' => 'PROD2 (MTN)', 'level' => 0, 'parent' => 1130, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1133', 'department' => 'PROD2 (MTN)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1134', 'department' => 'PROD2 (PCE ASSY)', 'level' => 0, 'parent' => 1130, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1135', 'department' => 'PROD2 (PCE)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1136', 'department' => 'PROD2 (PPC ASSY)', 'level' => 0, 'parent' => 1130, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1137', 'department' => 'PROD2 (PPC)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1138', 'department' => 'PROD2 (WH JT ASSY)', 'level' => 0, 'parent' => 1130, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1139', 'department' => 'PROD2 (WH JT)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1140', 'department' => 'PROD2 (HRD)', 'level' => 0, 'parent' => 1130, 'alloc' => 'GEN', 'status' => 1],

            ['dpt_id' => '1151', 'department' => 'PROD3 (GA)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1152', 'department' => 'PROD3 (MTN ASSY)', 'level' => 0, 'parent' => 1150, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1153', 'department' => 'PROD3 (MTN)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1154', 'department' => 'PROD3 (PCE ASSY)', 'level' => 0, 'parent' => 1150, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1155', 'department' => 'PROD3 (PCE)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1156', 'department' => 'PROD3 (PPC ASSY)', 'level' => 0, 'parent' => 1150, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1157', 'department' => 'PROD3 (PPC)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1158', 'department' => 'PROD3 (WH JT ASSY)', 'level' => 0, 'parent' => 1150, 'alloc' => 'ASSY 2W', 'status' => 1],
            ['dpt_id' => '1159', 'department' => 'PROD3 (WH JT)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1160', 'department' => 'PROD3 (HRD)', 'level' => 0, 'parent' => 1150, 'alloc' => 'GEN', 'status' => 1],

            ['dpt_id' => '1211', 'department' => 'PROD4 (GA)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1212', 'department' => 'PROD4 (MTN GRINDING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL GRINDING', 'status' => 1],
            ['dpt_id' => '1213', 'department' => 'PROD4 (MTN PLATING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL PLATING', 'status' => 1],
            ['dpt_id' => '1214', 'department' => 'PROD4 (MTN)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1215', 'department' => 'PROD4 (PCE GRINDING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL GRINDING', 'status' => 1],
            ['dpt_id' => '1216', 'department' => 'PROD4 (PCE PLATING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL PLATING', 'status' => 1],
            ['dpt_id' => '1217', 'department' => 'PROD4 (PCE)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1218', 'department' => 'PROD4 (PPC GRINDING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL GRINDING', 'status' => 1],
            ['dpt_id' => '1219', 'department' => 'PROD4 (PPC PLATING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL PLATING', 'status' => 1],
            ['dpt_id' => '1220', 'department' => 'PROD4 (PPC)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1221', 'department' => 'PROD4 (WH JT GRINDING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL GRINDING', 'status' => 1],
            ['dpt_id' => '1222', 'department' => 'PROD4 (WH JT PLATING)', 'level' => 0, 'parent' => 1210, 'alloc' => 'ALL PLATING', 'status' => 1],
            ['dpt_id' => '1223', 'department' => 'PROD4 (WH JT)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1224', 'department' => 'PROD4 (HRD)', 'level' => 0, 'parent' => 1210, 'alloc' => 'GEN', 'status' => 1],

            ['dpt_id' => '1231', 'department' => 'PROD5 (GA)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1232', 'department' => 'PROD5 (MTN ALL ASSY)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY 4W', 'status' => 1],
            ['dpt_id' => '1233', 'department' => 'PROD5 (MTN ASSY GAS)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY GAS 4W', 'status' => 1],
            ['dpt_id' => '1234', 'department' => 'PROD5 (MTN ASSY OIL)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY OIL 4W', 'status' => 1],
            ['dpt_id' => '1235', 'department' => 'PROD5 (MTN)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1236', 'department' => 'PROD5 (PCE ASSY)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY', 'status' => 1],
            ['dpt_id' => '1237', 'department' => 'PROD5 (PCE)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1238', 'department' => 'PROD5 (PPC ASSY)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY', 'status' => 1],
            ['dpt_id' => '1239', 'department' => 'PROD5 (PPC)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1240', 'department' => 'PROD5 (WH JT ASSY)', 'level' => 0, 'parent' => 1230, 'alloc' => 'ASSY', 'status' => 1],
            ['dpt_id' => '1241', 'department' => 'PROD5 (WH JT)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],
            ['dpt_id' => '1242', 'department' => 'PROD5 (HRD)', 'level' => 0, 'parent' => 1230, 'alloc' => 'GEN', 'status' => 1],

            ['dpt_id' => '1311', 'department' => 'PPC & INVENTORY', 'level' => 0, 'parent' => 1310, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1321', 'department' => 'MAINTENANCE', 'level' => 0, 'parent' => 1320, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1331', 'department' => 'WH FG', 'level' => 0, 'parent' => 1330, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1332', 'department' => 'WH CKDRM', 'level' => 0, 'parent' => 1330, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1333', 'department' => 'WH JIGTOOLS', 'level' => 0, 'parent' => 1330, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1334', 'department' => 'WH PACKAGING', 'level' => 0, 'parent' => 1330, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1341', 'department' => 'PCE 2W', 'level' => 0, 'parent' => 1340, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1351', 'department' => 'PCE 4W', 'level' => 0, 'parent' => 1350, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1361', 'department' => 'PCE', 'level' => 0, 'parent' => 1360, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '1411', 'department' => 'PROD SYSTEM', 'level' => 0, 'parent' => 1410, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '2111', 'department' => 'PDE 2W', 'level' => 0, 'parent' => 2110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '2121', 'department' => 'PDE 4W', 'level' => 0, 'parent' => 2120, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '3111', 'department' => 'CQE 2W', 'level' => 0, 'parent' => 3110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '3131', 'department' => 'CQE 4W', 'level' => 0, 'parent' => 3130, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '3121', 'department' => 'QA', 'level' => 0, 'parent' => 3120, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4111', 'department' => 'HRD & IR', 'level' => 0, 'parent' => 4110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4131', 'department' => 'GA', 'level' => 0, 'parent' => 4130, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4141', 'department' => 'MIS', 'level' => 0, 'parent' => 4140, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4151', 'department' => 'CPC', 'level' => 0, 'parent' => 4150, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4161', 'department' => 'PROCUREMENT', 'level' => 0, 'parent' => 4160, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4171', 'department' => 'GEN PURCHASE', 'level' => 0, 'parent' => 4170, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4181', 'department' => 'VDD', 'level' => 0, 'parent' => 4180, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4211', 'department' => 'EHS', 'level' => 0, 'parent' => 4210, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '4311', 'department' => 'COMMITTEE', 'level' => 0, 'parent' => 4310, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '5111', 'department' => 'MARKETING', 'level' => 0, 'parent' => 5110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '6111', 'department' => 'FIN & ACC', 'level' => 0, 'parent' => 6110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '6121', 'department' => 'PLAN & BUDGET', 'level' => 0, 'parent' => 6120, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '7111', 'department' => 'BOARD OF DIRECTOR', 'level' => 0, 'parent' => 7110, 'alloc' => null, 'status' => 1],
            ['dpt_id' => '7211', 'department' => 'TECHNICAL ADVISOR', 'level' => 0, 'parent' => 7210, 'alloc' => null, 'status' => 1],
        ]);
    }
}
