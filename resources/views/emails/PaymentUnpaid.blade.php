<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Registration 2nd Banten Urology Symposium 2025</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333333;
            text-align: center;
            margin-bottom: 30px;
        }
        p {
            color: #555555;
            line-height: 1.6;
        }
        .highlight {
            font-weight: bold;
            color: #333333;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Receipt Registration 2nd Banten Urology Symposium 2025</h2>
        
        <p>Dear {{ $payment->registrasi->full_name }},</p>
        <p>Thank you for  your registration.
        Allow us to attach proof of registration for the 2nd BUS 2025.
        If you have any inquiries or need further assistance, please don’t hesitate to reach out to the contact number: +62811-2694-088.</p>

        <!-- <p><span class="highlight">Invoice Number:</span> {{ $payment->invoice_number }}</p>
        <p><span class="highlight">Payment Amount:</span> Rp {{ number_format($payment->amount) }}</p>
        <p><span class="highlight">Status:</span> {{ strtoupper($payment->status) }}</p> -->


        <p>Thank you for your participation!</p>

        <div class="footer">
            <p>Best regards,</p>
            <p>2nd Banten Urology Symposium</p>
        </div>
    </div>
</body>
</html>
