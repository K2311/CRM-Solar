<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Quote;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use \App\Traits\HasTenant;

    public function index(Request $request)
    {
        $query = Payment::with('customer', 'quote');
        if ($request->customer_id) $query->where('customer_id', $request->customer_id);
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20)->withQueryString();
        $totalCollected = Payment::sum('amount');
        return view('payments.index', compact('payments', 'totalCollected'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $quotes = Quote::with('customer')->whereIn('status', ['sent', 'accepted'])->get();
        
        $selectedCustomer = $request->customer_id ? Customer::find($request->customer_id) : null;
        $selectedQuote = $request->quote_id ? Quote::find($request->quote_id) : null;
        
        // If quote is selected but no customer, set customer from quote
        if ($selectedQuote && !$selectedCustomer) {
            $selectedCustomer = $selectedQuote->customer;
        }

        return view('payments.create', compact('customers', 'quotes', 'selectedCustomer', 'selectedQuote'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'quote_id'     => 'nullable|exists:quotes,id',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'required|in:cash,bank_transfer,cheque,card,online',
            'reference'    => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $company = $this->tenantRequired();
        $data['company_id'] = $company->id;

        Payment::create($data);

        if ($request->quote_id) {
            return redirect()->route('quotes.show', $request->quote_id)->with('success', 'Payment recorded.');
        }

        if ($request->customer_id) {
            return redirect()->route('customers.show', $request->customer_id)->with('success', 'Payment recorded.');
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
