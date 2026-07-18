<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationMilestone extends Model
{
    protected $fillable = [
        'installation_id', 'milestone_number', 'name', 'status', 'completed_at', 'photo_path', 'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function installation() { return $this->belongsTo(Installation::class); }

    public static function defaultMilestones(): array
    {
        return [
            1  => 'Material Procurement',
            2  => 'Material Delivery & Verification',
            3  => 'Structure Installation',
            4  => 'Panel Mounting',
            5  => 'Inverter & BOS Wiring',
            6  => 'Earthing & ACDB/DCDB Work',
            7  => 'DISCOM Net-Meter Application',
            8  => 'DISCOM Site Inspection',
            9  => 'Net-Meter Installation & Commissioning',
            10 => 'Subsidy Disbursement Claim',
        ];
    }
}
