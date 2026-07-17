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
