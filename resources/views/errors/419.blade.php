<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>419 - Session Expired</title>
  <style>
    /* ===== RESET ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background-color: #0a0a0a;
      color: #fff;
      overflow: hidden;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url('/assets/admin/images/error.svg') center/cover no-repeat;
      opacity: 0.08;
      z-index: 0;
      filter: blur(2px);
    }

    body::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.7), #0a0a0a);
      z-index: 1;
    }

    main {
      position: relative;
      z-index: 2;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 20px;
    }

    .card {
      background: rgba(15, 15, 15, 0.6);
      backdrop-filter: blur(12px);
      padding: 40px 30px;
      border-radius: 24px;
      border: 1px solid rgba(255,255,255,0.06);
      width: 90%;
      max-width: 500px;
      box-shadow:
        0 0 25px rgba(250, 204, 21, 0.12),
        0 0 5px rgba(255,255,255,0.04);
      animation: float 4s ease-in-out infinite alternate;
    }

    @keyframes float {
      from { transform: translateY(0px); }
      to { transform: translateY(-10px); }
    }

    h1 {
      font-size: clamp(120px, 18vw, 220px);
      font-weight: 800;
      background: linear-gradient(90deg, #facc15, #f97316, #ef4444);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 12px;
      letter-spacing: -2px;
    }

    h2 {
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 24px;
      color: #f2f2f2;
      opacity: 0.85;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: linear-gradient(90deg, #facc15, #f97316);
      color: #000;
      padding: 12px 28px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      margin: 5px;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(250,204,21,0.4);
    }

    .btn i {
      font-size: 1.2rem;
    }

    @media (max-width: 600px) {
      h1 {
        font-size: clamp(80px, 25vw, 160px);
      }
      h2 {
        font-size: 1.4rem;
      }
      .btn {
        padding: 10px 22px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <main>
    <div class="card">
      <h1>419</h1>
      <h2>{{ __('Your session has expired!') }}</h2>
      <a href="{{ route('admin.login') }}" class="btn">
        {{ __('Back to Login') }}
        <i class="ph ph-arrow-circle-right"></i>
      </a>
      <a href="javascript:window.location.reload()" class="btn">
        {{ __('Reload') }}
        <i class="ph ph-arrow-circle-right"></i>
      </a>
    </div>
  </main>
</body>

</html>
