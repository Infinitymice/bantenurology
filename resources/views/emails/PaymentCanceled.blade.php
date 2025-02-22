<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled</title>
</head>
<body>
    <h2>Hello {{ $registrasi->full_name }},</h2>

    <p>We are sorry to inform you that your payment with invoice number {{ $invoice_number }} has been CANCELED due to expiration.</p>

    <p>Please make sure to complete your payment before the expiration date to avoid cancellation.</p>

    <p>If you have any questions, feel free to contact us.</p>

    <p>Best regards,</p>
    <p>Banten Urology</p>
</body>
</html>
