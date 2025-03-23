<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finance Record #{{ $finance['id'] }} - YNS</title>
  <style>
    :root {
      --yns-dark-blue: #1a237e;
      --yns-yellow: #ffd700;
      --yns-dark-gray: #2d2d2d;
      --yns-purple: #9022bb;
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      margin: 0;
      padding: 5px;
      color: var(--yns-dark-gray);
    }

    p,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      margin: 0;
      padding: 0;
    }

    /* Update the existing CSS */
    .section {
      margin: 5px 0;
      /* Reduced from 10px */
      padding: 3px;
      /* Reduced from 5px */
      background: #f8f9fa;
      border-radius: 5px;
    }

    .section-title {
      color: var(--yns-dark-blue);
      border-bottom: 1px solid var(--yns-green);
      /* Reduced from 2px */
      padding-bottom: 3px;
      /* Reduced from 5px */
      margin-bottom: 8px;
      /* Reduced from 15px */
      font-size: 14px;
      /* Add this to reduce title size */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 5px 0;
      /* Reduced from 10px */
      table-layout: fixed;
      /* Add this for equal columns */
    }

    th,
    td {
      padding: 3px 5px;
      /* Reduced padding */
      text-align: left;
      border-bottom: 1px solid #ddd;
      width: 50%;
      /* Force equal width columns */
    }

    /* Add this for the amounts column */
    td:last-child,
    th:last-child {
      text-align: right;
      /* Align amounts to the right */
    }

    /* Optimize the header spacing */
    .header {
      position: relative;
      border-bottom: 2px solid var(--yns-yellow);
      /* Reduced from 3px */
      margin-bottom: 10px;
      padding-bottom: 5px;
    }

    .company-info {
      text-align: right;
      font-size: 12px;
      /* Reduced from 14px */
      color: var(--yns-dark-blue);
      padding-bottom: 5px;
      /* Reduced from 10px */
      line-height: 1.2;
      /* Add this to reduce line height */
    }

    /* Optimize the document title */
    .document-title {
      text-align: center;
      color: var(--yns-dark-blue);
      font-size: 16px;
      /* Reduced from 18px */
      margin: 5px 0;
      display: flex;
      flex-direction: row;
      gap: 1rem
        /* Reduced from 2rem */
    }

    /* Update footer to take less space */
    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      padding: 5px 0;
      /* Reduced from 10px */
      text-align: center;
      font-size: 10px;
      /* Reduced from 12px */
      border-top: 1px solid var(--yns-yellow);
      /* Reduced from 2px */
    }

    .logo {
      position: absolute;
      top: 0;
      left: 0;
      width: 40px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
    }

    th,
    td {
      padding: 5px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: var(--yns-purple);
      color: white;
    }

    .total-row {
      font-weight: bold;
      background-color: #f0f0f0;
    }

    .profit {
      color: green;
    }

    .loss {
      color: red;
    }
  </style>
</head>

<body>
  <div class="header">
    <img src="{{ public_path('images/system/yns_logo.png') }}" class="logo" alt="YNS Logo">
    <div class="company-info">
      <h3>{{ ucfirst($finance['service_type']) }}: {{ ucfirst($finance['service_name']) }}
      </h3>
      <p>{{ $finance['service_address'] }}</p>
      @if ($finance['service_phone'])
        <p>Tel: {{ $finance['service_phone'] }}</p>
      @endif
      @if ($finance['service_email'])
        <p>Email: {{ $finance['service_email'] }}</p>
      @endif
    </div>
  </div>
  </div>

  <div class="document-title">
    <h2>{{ $finance['name'] }}</h2>
  </div>

  <div class="section">
    <h3 class="section-title">Event Details</h3>
    <table>
      <tr>
        <th>Date Range</th>
        <td>{{ \Carbon\Carbon::parse($finance['date_from'])->format('j F Y') }} -
          {{ \Carbon\Carbon::parse($finance['date_to'])->format('j F Y') }}</td>
      </tr>
      @if ($finance['external_link'])
        <tr>
          <th>Event Link</th>
          <td>{{ $finance['external_link'] }}</td>
        </tr>
      @endif
      <tr>
        <th>Desired Profit</th>
        <td>£{{ number_format($finance['desired_profit'], 2) }}</td>
      </tr>
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Income</h3>
    <table>
      <tr>
        <th style="width: 50%">Category</th>
        <th style="width: 50%">Amount</th>
      </tr>
      <tr>
        <td style="width: 50%">Presale Tickets</td>
        <td style="width: 50%">£{{ number_format($finance['income_presale'], 2) }}</td>
      </tr>
      <tr>
        <td style="width: 50%">On The Door Tickets</td>
        <td style="width: 50%">£{{ number_format($finance['income_otd'], 2) }}</td>
      </tr>
      @if (!empty($finance['other_income_items']))
        @foreach ($finance['other_income_items'] as $item)
          <tr>
            <td style="width: 50%">{{ $item['label'] }}</td>
            <td style="width: 50%">£{{ number_format($item['value'], 2) }}</td>
          </tr>
        @endforeach
      @endif
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Outgoings</h3>
    <table>
      <tr>
        <th style="width: 50%">Category</th>
        <th style="width: 50%">Amount</th>
      </tr>
      <tr>
        <td style="width: 50%">Venue</td>
        <td style="width: 50%">£{{ number_format($finance['outgoing_venue'], 2) }}</td>
      </tr>
      <tr>
        <td style="width: 50%">Artist(s)</td>
        <td style="width: 50%">£{{ number_format($finance['outgoing_band'], 2) }}</td>
      </tr>
      <tr>
        <td style="width: 50%">Promotion</td>
        <td style="width: 50%">£{{ number_format($finance['outgoing_promotion'], 2) }}</td>
      </tr>
      <tr>
        <td style="width: 50%">Rider</td>
        <td style="width: 50%">£{{ number_format($finance['outgoing_rider'], 2) }}</td>
      </tr>
      @if (!empty($finance['other_outgoing_items']))
        @foreach ($finance['other_outgoing_items'] as $item)
          <tr>
            <td style="width: 50%">{{ $item['label'] }}</td>
            <td style="width: 50%">£{{ number_format($item['value'], 2) }}</td>
          </tr>
        @endforeach
      @endif
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Summary</h3>
    <table>
      <tr>
        <th style="width: 50%">Category</th>
        <th style="width: 50%">Amount</th>
      </tr>
      <tr>
        <td style="width: 50%">Total Income</td>
        <td style="width: 50%">£{{ number_format($finance['total_incoming'], 2) }}</td>
      </tr>
      <tr>
        <td style="width: 50%">Total Outgoings</td>
        <td style="width: 50%">£{{ number_format($finance['total_outgoing'], 2) }}</td>
      </tr>
      <tr class="total-row">
        <td style="width: 50%">Net Profit/Loss</td>
        <td style="width: 50%"
          class="{{ $finance['total_incoming'] - $finance['total_outgoing'] >= 0 ? 'profit' : 'loss' }}">
          £{{ number_format($finance['total_incoming'] - $finance['total_outgoing'], 2) }}
        </td>
      </tr>
    </table>
  </div>

  <div class="footer">
    Generated on {{ now()->format('j F Y') }} by {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}.
    Finance ID #{{ $finance['id'] }}
  </div>

  <script type="text/php">
    if ($pdf) {
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $size = 10;
        $font = $fontMetrics->getFont("DejaVu Sans");
        $x = $pdf->get_width() - 80;
        $y = $pdf->get_height() - 25;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>

</html>
