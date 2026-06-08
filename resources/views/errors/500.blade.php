<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 - Internal Server Error</title>
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
      opacity: 0.06;
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
        0 0 25px rgba(239, 68, 68, 0.12),
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
      background: linear-gradient(90deg, #ef4444, #f97316, #facc15);
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

    p {
      font-size: 1rem;
      color: #ccc;
      margin-bottom: 32px;
      line-height: 1.6;
      opacity: 0.65;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: linear-gradient(90deg, #ef4444, #f97316);
      color: #000;
      padding: 12px 28px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(239,68,68,0.4);
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
      <h1>500</h1>
      <h2>Internal Server Error</h2>
      <p>Oops! Something went wrong on our end. Please try again later.</p>
      <a href="/" class="btn">Go Back Home</a>
    </div>
  </main>
</body>

</html>
