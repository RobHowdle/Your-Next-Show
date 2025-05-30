<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance Mode | Your Next Show</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --color-orange: #ffbc00;
      --color-purple: #9429ff;
      --color-black: rgba(0, 0, 0, 0.8);
      --color-yellow: #ffd800;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Nunito', sans-serif;
      /* Match the welcome page background */
      background: linear-gradient(to bottom, var(--color-orange), var(--color-purple));
      color: white;
      min-height: 100vh;
      overflow-x: hidden;
      position: relative;
    }

    /* Background overlay like welcome page */
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.3);
      z-index: 0;
    }

    .page-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      position: relative;
      z-index: 1;
    }

    .container {
      text-align: center;
      background: rgba(0, 0, 0, 0.3);
      padding: 3rem;
      border-radius: 15px;
      max-width: 650px;
      width: 100%;
      box-shadow: 0 8px 25px -5px rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
    }

    .logo {
      margin-bottom: 2rem;
      max-width: 200px;
      height: auto;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
      font-weight: 700;
      color: white;
    }

    h1 span {
      display: block;
      font-size: 1.5rem;
      font-weight: normal;
      color: var(--color-yellow);
      margin-top: 0.5rem;
    }

    p {
      font-size: 1.25rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }

    .message {
      background: rgba(255, 255, 255, 0.1);
      padding: 15px;
      border-radius: 10px;
      margin-top: 1.5rem;
      border-left: 4px solid var(--color-yellow);
    }

    .icons {
      margin-top: 2.5rem;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .icons a {
      color: white;
      font-size: 1.5rem;
      transition: color 0.3s;
    }

    .icons a:hover {
      color: var(--color-yellow);
    }

    .rejoin-button {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color: var(--color-yellow);
      color: black;
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      margin-top: 1.5rem;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .rejoin-button:hover {
      background-color: #e6c400;
    }

    @media (max-width: 640px) {
      .container {
        padding: 2rem;
      }

      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 1.1rem;
      }
    }
  </style>
</head>

<body>
  <div class="page-wrapper">
    <div class="container">
      <!-- Add your logo here -->
      <img src="{{ asset('images/system/yns_logo.png') }}" alt="Your Next Show" class="logo"
        onerror="this.style.display='none'">

      <h1>
        Your Next Show
        <span>Will Be Back Soon</span>
      </h1>

      <p>We're currently making some improvements to better connect bands with venues across the UK.</p>

      <p>Thank you for your patience while we enhance your experience.</p>

      @if (isset($data['message']))
        <div class="message">
          <p>{{ $data['message'] }}</p>
        </div>
      @endif
    </div>
  </div>
</body>

</html>
