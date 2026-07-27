<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use \App\Traits\HasTenant;

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_type' => 'required|string',
            'subject_id'   => 'required|integer',
            'type'         => 'required|in:note,call,email,meeting,task',
            'title'        => 'nullable|string|max:255',
            'description'  => 'required|string',
            'due_at'       => 'nullable|date',
        ]);

        $company = $this->tenantRequired();
        
        $allowedTypes = [
            'App\Models\Lead',
            'App\Models\Customer',
            'App\Models\Quote',
            'App\Models\Installation',
            'App\Models\ServiceTicket',
        ];

        if (!in_array($data['subject_type'], $allowedTypes)) {
            abort(403, 'Invalid subject type.');
        }

        $subjectClass = $data['subject_type'];
        $subject = $subjectClass::find($data['subject_id']);
        
        if (!$subject || $subject->company_id !== $company->id) {
            abort(404, 'Subject not found or access denied.');
        }
        Activity::create(array_merge($data, [
            'company_id'   => $company->id,
            'user_id'      => auth()->id(),
        ]));

        return back()->with('success', 'Activity logged.');
    }

    public function destroy(Activity $activity)
    {
        abort_if($activity->user_id !== auth()->id() && !auth()->user()->isAdmin(), 403);
        $activity->delete();
        return back()->with('success', 'Activity deleted.');
    }
}
