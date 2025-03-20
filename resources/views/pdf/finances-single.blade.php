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
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      margin: 0;
      padding: 20px;
      color: var(--yns-dark-gray);
    }

    .header {
      position: relative;
      padding: 20px;
      margin-bottom: 30px;
      border-bottom: 3px solid var(--yns-yellow);
    }

    .logo {
      position: absolute;
      top: 0;
      left: 0;
      width: 100px;
    }

    .company-info {
      text-align: right;
      font-size: 14px;
      color: var(--yns-dark-blue);
    }

    .document-title {
      text-align: center;
      color: var(--yns-dark-blue);
      font-size: 24px;
      margin: 40px 0;
    }

    .section {
      margin: 20px 0;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 5px;
    }

    .section-title {
      color: var(--yns-dark-blue);
      border-bottom: 2px solid var(--yns-yellow);
      padding-bottom: 5px;
      margin-bottom: 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
    }

    th,
    td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: var(--yns-dark-blue);
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

    .footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      padding: 10px 0;
      text-align: center;
      font-size: 12px;
      border-top: 2px solid var(--yns-yellow);
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
    <h1>Budget #{{ $finance['id'] }}</h1>
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
        <th>Category</th>
        <th>Amount</th>
      </tr>
      <tr>
        <td>Presale Tickets</td>
        <td>£{{ number_format($finance['income_presale'], 2) }}</td>
      </tr>
      <tr>
        <td>On The Door Tickets</td>
        <td>£{{ number_format($finance['income_otd'], 2) }}</td>
      </tr>
      @if (!empty($finance['other_income_items']))
        @foreach ($finance['other_income_items'] as $item)
          <tr>
            <td>{{ $item['label'] }}</td>
            <td>£{{ number_format($item['value'], 2) }}</td>
          </tr>
        @endforeach
      @endif
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Outgoings</h3>
    <table>
      <tr>
        <th>Category</th>
        <th>Amount</th>
      </tr>
      <tr>
        <td>Venue</td>
        <td>£{{ number_format($finance['outgoing_venue'], 2) }}</td>
      </tr>
      <tr>
        <td>Artist(s)</td>
        <td>£{{ number_format($finance['outgoing_band'], 2) }}</td>
      </tr>
      <tr>
        <td>Promotion</td>
        <td>£{{ number_format($finance['outgoing_promotion'], 2) }}</td>
      </tr>
      <tr>
        <td>Rider</td>
        <td>£{{ number_format($finance['outgoing_rider'], 2) }}</td>
      </tr>
      @if (!empty($finance['other_outgoing_items']))
        @foreach ($finance['other_outgoing_items'] as $item)
          <tr>
            <td>{{ $item['label'] }}</td>
            <td>£{{ number_format($item['value'], 2) }}</td>
          </tr>
        @endforeach
      @endif
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Summary</h3>
    <table>
      <tr>
        <th>Category</th>
        <th>Amount</th>
      </tr>
      <tr>
        <td>Total Income</td>
        <td>£{{ number_format($finance['total_incoming'], 2) }}</td>
      </tr>
      <tr>
        <td>Total Outgoings</td>
        <td>£{{ number_format($finance['total_outgoing'], 2) }}</td>
      </tr>
      <tr class="total-row">
        <td>Net Profit/Loss</td>
        <td class="{{ $finance['total_incoming'] - $finance['total_outgoing'] >= 0 ? 'profit' : 'loss' }}">
          £{{ number_format($finance['total_incoming'] - $finance['total_outgoing'], 2) }}
        </td>
      </tr>
    </table>
  </div>

  <div class="footer">
    Generated on {{ now()->format('j F Y') }} by {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
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
