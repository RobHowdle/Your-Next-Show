<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finance Record Export</title>
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
      <h3>{{ ucfirst($data['company']['type']) }}: {{ $data['company']['name'] }}</h3>
      @if ($data['company']['address'])
        <p>{{ $data['company']['address'] }}</p>
      @endif
      @if ($data['company']['phone'])
        <p>Tel: {{ $data['company']['phone'] }}</p>
      @endif
      @if ($data['company']['email'])
        <p>Email: {{ $data['company']['email'] }}</p>
      @endif
    </div>
  </div>

  <div class="document-title">
    <h2>Financial Report - {{ ucfirst($data['filter']) }}</h2>
  </div>

  <div class="section">
    <h3 class="section-title">Summary</h3>
    <table>
      <tr>
        <th style="width: 25%">Date</th>
        <th style="width: 25%">Total Income</th>
        <th style="width: 25%">Total Outgoing</th>
        <th style="width: 25%">Net Profit/Loss</th>
      </tr>
      @foreach ($data['records'] as $record)
        <tr class="total-row">
          <td style="width: 25%">{{ \Carbon\Carbon::parse($record['date'])->format('d/m/Y') }}</td>
          <td style="width: 25%">£{{ number_format($record['income'], 2) }}</td>
          <td style="width: 25%">£{{ number_format($record['outgoing'], 2) }}</td>
          <td style="width: 25%" class="{{ $record['profit'] >= 0 ? 'profit' : 'loss' }}">
            £{{ number_format($record['profit'], 2) }}
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <div class="section">
    <h3 class="section-title">Period Totals</h3>
    <table>
      <tr>
        <th style="width: 50%">Category</th>
        <th style="width: 50%">Amount</th>
      </tr>
      <tr>
        <td>Total Income</td>
        <td>£{{ number_format($data['totals']['income'], 2) }}</td>
      </tr>
      <tr>
        <td>Total Outgoing</td>
        <td>£{{ number_format($data['totals']['outgoing'], 2) }}</td>
      </tr>
      <tr class="total-row">
        <td>Net Profit/Loss</td>
        <td class="{{ $data['totals']['profit'] >= 0 ? 'profit' : 'loss' }}">
          £{{ number_format($data['totals']['profit'], 2) }}
        </td>
      </tr>
    </table>
  </div>

  <div class="footer">
    Generated on {{ now()->format('j F Y') }} by {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}.
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
