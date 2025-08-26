<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkcenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('workcenters')->insert([
            ['wct_id' => 'RC_CYC', 'workcenter' => 'Cylinder & O/S Cutting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_CYM', 'workcenter' => 'Cylinder Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FA1', 'workcenter' => 'Front Fork Assy 1', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FA2', 'workcenter' => 'Front Fork Assy 2', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FA3', 'workcenter' => 'Front Fork Assy 3', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FA4', 'workcenter' => 'Front Fork Assy 4', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FA5', 'workcenter' => 'Front Fork Assy 5', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FAA', 'workcenter' => 'Front Fork Assy All', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FPC', 'workcenter' => 'Front Fork Packaging', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FPN', 'workcenter' => 'PAINTING', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_FPT', 'workcenter' => 'Painting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITB', 'workcenter' => 'Inner Tube Buffing', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITC', 'workcenter' => 'Inner Tube Cutting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITG', 'workcenter' => 'Inner Tube Grinding', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITK', 'workcenter' => 'Inner Tube Baking', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITM', 'workcenter' => 'Inner Tube Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_ITP', 'workcenter' => 'Inner Tube Plating', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OA1', 'workcenter' => 'OCU Assy 1', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OA2', 'workcenter' => 'OCU Assy 2', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OA3', 'workcenter' => 'OCU Assy 3', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OA4', 'workcenter' => 'OCU Assy 4', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OA5', 'workcenter' => 'OCU Assy 5', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OCC', 'workcenter' => 'O/Shell Cylinder Complete', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OPC', 'workcenter' => 'OCU Packaging', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OTB', 'workcenter' => 'Outer Tube Buffing', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OTC', 'workcenter' => 'Outer Tube Casting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_OTM', 'workcenter' => 'Outer Tube Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PRH', 'workcenter' => 'Piston Rod Hardening', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PT1', 'workcenter' => 'Painting 1', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PT2', 'workcenter' => 'Painting 2', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PT3', 'workcenter' => 'Painting 3', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PT4', 'workcenter' => 'Painting 4', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_PT5', 'workcenter' => 'Painting 5', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_SSM', 'workcenter' => 'Steering Shaft Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_UBC', 'workcenter' => 'Under Bracket Complete', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_UBL', 'workcenter' => 'Under Bracket Tebal', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_UBM', 'workcenter' => 'Under Bracket Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_UBS', 'workcenter' => 'Under Bracket Tipis', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_UUB', 'workcenter' => 'Upper Under Bracket', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_VDP', 'workcenter' => 'Vendor Development', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_WS2', 'workcenter' => 'Workshop 2W', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC_WWT', 'workcenter' => 'WWT', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RC01', 'workcenter' => 'NOT USED', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_CLC', 'workcenter' => 'CLEANING CENTER', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_CTP', 'workcenter' => 'Cutting Pipe', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_MS6', 'workcenter' => 'Mounting SA Assy 6', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_MSA', 'workcenter' => 'Mounting SA Assy', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_MSD', 'workcenter' => 'Mounting Stay Damper', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_MST', 'workcenter' => 'Mounting Strut', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_OS2', 'workcenter' => 'Outer Shell Complete 2', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_OSC', 'workcenter' => 'Outer Shell Complete', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PPS', 'workcenter' => 'Power Press', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PRB', 'workcenter' => 'Piston Rod Buffing', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PRC', 'workcenter' => 'Piston Rod Cutting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PRG', 'workcenter' => 'Piston Rod Grinding', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PRM', 'workcenter' => 'Piston Rod Machining', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_PRP', 'workcenter' => 'Piston Rod Plating', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA1', 'workcenter' => 'SA Assy 1', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA2', 'workcenter' => 'SA Assy 2', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA3', 'workcenter' => 'SA Assy 3', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA4', 'workcenter' => 'SA Assy 4', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA5', 'workcenter' => 'SA Assy 5', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA6', 'workcenter' => 'SA Assy 6', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA7', 'workcenter' => 'SA Assy 7', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA8', 'workcenter' => 'SA Assy 8', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SA9', 'workcenter' => 'SA Assy 9', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SAA', 'workcenter' => 'All SA Assy', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SAG', 'workcenter' => 'SA Assy Gas', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SAO', 'workcenter' => 'SA Assy Oil', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SAS', 'workcenter' => 'Sub Assy', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SPC', 'workcenter' => 'SA Packaging', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SPT', 'workcenter' => 'SA Painting', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_SSD', 'workcenter' => 'SA Assy Stay Damper', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_WLC', 'workcenter' => 'WELDING CENTER', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'RP_WS4', 'workcenter' => 'Workshop 4W', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['wct_id' => 'TD04', 'workcenter' => 'NOT USED (buka lagi)', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
