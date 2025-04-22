<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Welcome to Your Next Show</title>
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      line-height: 1.6;
      color: #E5E7EB;
      /* gray-200 */
      background-color: #111827;
      /* gray-900 */
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      background-color: #000000;
      padding: 20px;
      text-align: center;
      border-radius: 8px 8px 0 0;
      border: 1px solid #374151;
      /* gray-700 */
    }

    .header h1 {
      margin: 0;
      font-weight: 600;
    }

    .content {
      padding: 20px;
      background: #1F2937;
      /* gray-800 */
      border: 1px solid #374151;
      /* gray-700 */
      border-top: none;
    }

    .welcome-message {
      font-size: 18px;
      margin-bottom: 20px;
      color: #E5E7EB;
      /* gray-200 */
    }

    .next-steps {
      background: #374151;
      /* gray-700 */
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
      border: 1px solid #4B5563;
      /* gray-600 */
    }

    .next-steps h3 {
      color: #FFD700;
      /* yns_yellow */
      margin-top: 0;
    }

    .button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #FFD700;
      /* yns_yellow */
      color: #000000;
      text-decoration: none;
      border-radius: 4px;
      margin-top: 15px;
      font-weight: 500;
    }

    .footer {
      text-align: center;
      padding: 20px;
      font-size: 12px;
      color: #9CA3AF;
      /* gray-400 */
      background: #111827;
      /* gray-900 */
      border: 1px solid #374151;
      /* gray-700 */
      border-top: none;
      border-radius: 0 0 8px 8px;
    }

    .footer a {
      color: #FFD700;
      /* yns_yellow */
      text-decoration: none;
    }

    h2 {
      color: #FFD700;
      /* yns_yellow */
      margin-top: 0;
    }

    p {
      margin: 10px 0;
      color: #E5E7EB;
      /* gray-200 */
    }

    .logo-container {
      text-align: center;
      margin-bottom: 1rem;
    }

    .brand-name {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      font-size: 2.25rem;
      font-weight: 700;
      color: #FFFFFF;
      margin: 0;
      line-height: 1.2;
    }

    .brand-tagline {
      display: block;
      font-size: 1.25rem;
      font-weight: normal;
      color: #FFD700;
      margin-top: 0.5rem;
    }

    .brand-subtext {
      display: block;
      font-size: 1rem;
      color: #9CA3AF;
      margin-top: 0.25rem;
    }

    .header {
      background-color: #000000;
      padding: 2rem;
      text-align: center;
      border-radius: 8px 8px 0 0;
      border: 1px solid #374151;
    }

    .header-content {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 2rem;
      padding: 2rem;
    }

    .logo-container {
      flex-shrink: 0;
      margin-bottom: 0;
    }

    .brand-content {
      text-align: left;
      flex-grow: 1;
    }

    .brand-name {
      font-size: 2rem;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="header-content">
        <div class="logo-container">
          @if ($logoExists)
            <img src="{{ $message->embedData(file_get_contents($logoPath), 'logo.png') }}" alt="Your Next Show Logo"
              width="120" height="160" style="width: 120px; height: 160px; display: block;">
          @endif
        </div>
        <div class="brand-content">
          <h1 class="brand-name">
            Your Next Show
            <span class="brand-tagline">
              Connecting Bands with Venues
              <span class="brand-subtext">and so much more...</span>
            </span>
          </h1>
        </div>
      </div>
    </div>

    <div class="content">
      <h2>Welcome to Your Next Show, {{ $user->first_name }}!</h2>

      <div class="welcome-message">
        @if ($user->roles->first())
          <p>Thanks for joining Your Next Show as a {{ $user->roles->first()->display_name }}!</p>
          <p>We're excited to have you on board and can't wait to help you {{ $user->roles->first()->description }}.</p>
        @else
          <p>Thanks for joining Your Next Show!</p>
          <p>We're excited to have you on board.</p>
        @endif
        <p>This email is to confirm that your account has successfully been created.</p>
      </div>

      <div class="next-steps">
        <h3>Next Steps:</h3>
        <p>You will receive an email shortly with a few next steps for you based on the type of account you have
          created.</p>
      </div>

      <p>Ready to get started?</p>
      <a href="{{ route('dashboard.index') }}" class="button">Go to Dashboard</a>
    </div>

    <div class="footer">
      <p>
        &copy; {{ date('Y') }} Your Next Show. All rights reserved.<br>
        If you didn't create this account, please <a href="{{ url('/contact') }}">contact us</a>.
      </p>
    </div>
  </div>
</body>

</html>
