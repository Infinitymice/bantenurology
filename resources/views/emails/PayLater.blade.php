<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Pay Later Confirmation</title>
</head>
<body>
    <div class="container">
        <h1>Payment Pay Later Confirmation</h1>
        <p>Thank you for choosing the "Pay Later" option for your event registration.</p>
        
        <p><span class="highlight">Invoice Number:</span> {{ $invoiceNumber }}</p>
        <p><span class="highlight">Total Payment:</span> Rp {{ number_format($amount) }}</p>
        <p><span class="highlight">Status:</span> {{ strtoupper($status) }}</p>
        <p><strong>Payment Expiry:</strong> {{ \Carbon\Carbon::parse($paymentExpiry)->format('d M Y H:i:s') }}</p>


        <p><span class="highlight">Bank Account Number:</span> 1234567890</p>
        <p><span class="highlight">Bank Account Name:</span> Infinity Mice Management</p>
        
        <p>Please make the payment to our account and upload the transfer proof through the 
        <a href="http://127.0.0.1:8000/pay-later" class="button">payment confirmation page</a>.</p>

        <div class="footer">
            <p>If you have any questions, feel free to contact our registration team.</p>
            <p>Thank you for your registration.</p>
        </div>
    </div>
</body>
</html>
