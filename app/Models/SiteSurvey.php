<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteSurvey extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id', 'lead_id', 'customer_id', 'technician_id', 'roof_area_sqft',
        'roof_type', 'shading_details', 'discom_name', 'sanctioned_load_kw',
        'consumer_number', 'photos', 'notes', 'survey_date',
    ];

    protected $casts = [
        'photos' => 'array',
        'survey_date' => 'date',
        'roof_area_sqft' => 'decimal:2',
        'sanctioned_load_kw' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function technician() { return $this->belongsTo(User::class, 'technician_id'); }
}
