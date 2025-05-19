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
      background-color: #111827;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
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
      text-align: center;
    }

    .brand-content {
      text-align: left;
      flex-grow: 1;
    }

    .brand-name {
      font-size: 2rem;
      font-weight: 700;
      color: #FFFFFF;
      margin: 0;
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

    .content {
      padding: 20px;
      background: #1F2937;
      border: 1px solid #374151;
      border-top: none;
    }

    h2 {
      color: #FFD700;
      margin-top: 0;
    }

    p {
      margin: 10px 0;
      color: #E5E7EB;
    }

    .next-steps {
      background: #374151;
      padding: 15px;
      border-radius: 4px;
      margin: 20px 0;
      border: 1px solid #4B5563;
    }

    .next-steps h3 {
      color: #FFD700;
      margin-top: 0;
    }

    .button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #FFD700;
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
      background: #111827;
      border: 1px solid #374151;
      border-top: none;
      border-radius: 0 0 8px 8px;
    }

    .footer a {
      color: #FFD700;
      text-decoration: none;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="header-content">
        <div class="logo-container">
          @if ($logoExists)
            <img src="{{ $message->embedData(file_get_contents($logoPath), 'logo.png') }}"
              alt="Your Next Show logo - Connecting Bands with Venues" width="120" height="160"
              style="width: 120px; height: 160px; display: block;">
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
        @else
          <p>Thanks for joining Your Next Show!</p>
        @endif

        <p>I'm Rob — the creator and developer of Your Next Show. I wanted the very first email you receive from us to
          include a message from me.</p>

        <p>I’m genuinely grateful you signed up and took a moment to see what we’re all about. Whether you’re sticking
          around for the encore or just catching the opening act to see what’s what — I’m just glad you’re here.</p>

        <p>YNS was built to bring people together in a safe, supportive, and fun environment. The fact that you’ve
          landed here, for whatever reason, tells me that maybe it’s starting to work.</p>

        <p>You’ll get another email shortly with some steps tailored to your role to help you get started.</p>

        <p>If you ever need anything, feel free to get in touch. Let’s build something awesome.</p>
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
