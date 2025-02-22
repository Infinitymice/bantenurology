<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .ticket-container {
            position: relative;
            /* width: 100%; */
            /* max-width: 600px; */
            height: 100%;
            margin: 0 auto;
            border: 1px solid #000;
            overflow: hidden;
            padding: 0px;
            background-image: url("{{ public_path('logo/bgtiket.jpg') }}");
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            color: #fff;
        }

        .ticket-content {
            /* background-color: rgba(255, 255, 255, 0.9); */
            /* padding: 20px; */
            border-radius: 8px;
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .ticket-header h3 {
            font-size: 18px;
            margin: 5px 0;
            color: #000;
        }

        .ticket-body {
            text-align: left;
            color: #333;
            margin-top: 230px;
            margin-left: 10px;
            margin-right: 10px;
            font-size: 1.7rem;
        }

        .ticket-info {
            width: 100%;
            font-size: 1.7rem !important;
        }

        .ticket-info td {
            padding: 10px;
            vertical-align: top;
            font-size: inherit;
        }

        .ticket-info p {
            margin: 0;
            font-size: inherit;
        }


        .row {
            margin-bottom: 8px;
        }

        .row span {
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            color: #333;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .barcode {
            text-align: center;
            margin-top: 60px;
            background: transparent;
        }

        .barcode img {
            background: transparent !important;
            mix-blend-mode: multiply;
            /* Ini akan membantu QR code lebih baik bercampur dengan background */
        }

        .terms {
            font-size: 10px;
            text-align: center;
            margin-top: 20px;
            color: #555;
        }

        #backdrop {
            position: relative;
            /* z-index: -1; */
        }

        #backdrop img {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <!-- <div id="backdrop">
        <img src="{{ public_path('logo/E TIKET.jpg') }}" alt="" srcset="">
    </div> -->
    <div class="ticket-container">
        <div class="ticket-content">
            <!-- Ticket Header -->
            <!-- <div class="ticket-header">
                <h3>Event Ticket</h3>
            </div> -->

            <div class="ticket-body">
                <div style="display: block; margin-bottom: 15px; padding: 10px;">
                    <table class="ticket-info">
                        <tr>
                            <td>
                                <p><strong>Participant Name:</strong><br>{{ $full_name }}</p>                            
                            </td>
                            <td style="text-align: right;">
                                <p><strong>Ticket Number:</strong><br>{{ $ticketNumber }}</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: center;">
                    <p><strong>Event(s):</strong></p>
                    <ul style="list-style-type: none; padding-left: 0;">
                        @foreach ($eventDetails as $eventDetail)
                        <li>{{ $eventDetail }}</li>
                        @endforeach
                    </ul>
                </div>

                @if(!empty($accommodations))
                <div style="text-align: center;">
                    <p><strong>Accommodation Details:</strong></p>
                    <ul style="list-style-type: none; padding-left: 0;">
                        @foreach($accommodations as $accommodation)
                        <li>
                            {{ $accommodation['name'] }}<br>
                            Quantity: {{ $accommodation['quantity'] }}<br>
                            Check-in: {{ $accommodation['check_in'] }}<br>
                            Check-out: {{ $accommodation['check_out'] }}
                        </li>
                        @if(!$loop->last)<br>@endif
                        @endforeach
                    </ul>
                </div>
                @endif


                <p style="text-align: center; margin-top: 20px;"><strong>Total Payment:</strong> Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>

            <!-- Additional Confirmation Message -->
            <!-- <div class="ticket-body">
                <p>Your payment has been successfully confirmed by the admin. Thank you for your purchase. Below is your ticket for the event. Please bring this ticket with you and present it at the entrance to gain access to the event.</p>
            </div> -->


            <!-- Terms -->
            <p class="terms">By attending the events, you are agree to the terms and conditions.</p>
        </div>
    </div>
    </div>
</body>

</html>