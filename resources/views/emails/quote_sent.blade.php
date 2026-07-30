<!DOCTYPE html>
<html>
<head>
    <title>Your Quote from {{ $quote->company->name ?? 'Us' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2>Hello {{ $quote->customer->name }},</h2>
    
    <p>We have prepared a new proposal for you! You can review the details, costs, and digitally accept it securely at the link below:</p>
    
    <div style="margin: 30px 0; text-align: center;">
        <a href="{{ route('public.quotes.show', $quote->public_token) }}" style="background-color: #0284c7; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
            View & Accept Proposal
        </a>
    </div>
    
    <p>Quote Number: <strong>{{ $quote->quote_number }}</strong></p>
    <p>Total Amount: <strong>₹{{ number_format($quote->net_cost ?? $quote->total, 2) }}</strong></p>
    
    <p>If you have any questions, please reply directly to this email or contact us.</p>
    
    <p>Best regards,<br>{{ $quote->company->name ?? 'The Team' }}</p>

</body>
</html>
