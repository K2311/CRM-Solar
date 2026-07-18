<?php

namespace App\Http\Controllers;

use App\Models\GstInvoice;
use Illuminate\Http\Request;

class GstInvoiceController extends Controller
{
    use \App\Traits\HasTenant;

    public function index()
    {
        $invoices = GstInvoice::with('customer', 'installation')->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function show(GstInvoice $invoice)
    {
        $invoice->load('customer', 'quote.items.product', 'company');
        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, GstInvoice $invoice)
    {
        $data = $request->validate([
            'status' => 'required|in:paid,unpaid',
        ]);

        $invoice->update(['status' => $data['status']]);

        return back()->with('success', 'Invoice status updated successfully.');
    }
}
