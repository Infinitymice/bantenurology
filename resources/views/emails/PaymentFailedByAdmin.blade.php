<!DOCTYPE html>
<html>
<head>
    <title>Payment Failure Notification</title>
</head>
<body>
    <p>Hello {{ $name }},</p>

    <p>We regret to inform you that your payment with invoice number <strong>{{ $invoiceNumber }} has FAILED.</strong></p>

    @if($failedReason)
        <p>Reason for failure: <strong>{{ $failedReason }}</strong></p>
    @endif

    <p>Please double-check and make the payment again through our system. If you have any questions, feel free to contact our team for further assistance.</p>

    <p>Thank you.</p>
</body>
</html>
