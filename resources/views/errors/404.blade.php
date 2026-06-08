<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>

    @php
        $appInfo = array_merge(config('application_info'));
        $primaryColor = $appInfo['theme']['primary_color'] ?? '#6366f1';
        $secondaryColor = $appInfo['theme']['secondary_color'] ?? '#facc15';
    @endphp

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }

        /* ===== RESET ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background: #000;
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
            background: radial-gradient(circle at top, rgba(250, 204, 21, 0.15), transparent 70%),
                linear-gradient(to bottom, rgba(0, 0, 0, 0.6), #000);
            z-index: 1;
        }

        .noise {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.08'/%3E%3C/svg%3E");
            opacity: 0.15;
            z-index: 1;
            pointer-events: none;
        }

        .scan {
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(transparent,
                    transparent 3px,
                    rgba(255, 255, 255, 0.02) 3px,
                    rgba(255, 255, 255, 0.02) 4px);
            z-index: 2;
            pointer-events: none;
        }

        main {
            position: relative;
            z-index: 3;
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
            border: 1px solid rgba(255, 255, 255, 0.06);
            width: 90%;
            max-width: 520px;
            box-shadow:
                0 0 25px rgba(250, 204, 21, 0.12),
                0 0 5px rgba(255, 255, 255, 0.04);
            animation: float 4s ease-in-out infinite alternate;
        }

        @keyframes float {
            from {
                transform: translateY(0px);
            }

            to {
                transform: translateY(-10px);
            }
        }

        h1 {
            font-size: clamp(90px, 18vw, 210px);
            font-weight: 800;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
            letter-spacing: -2px;
            opacity: 0.96;
        }

        h2 {
            font-size: 1.7rem;
            font-weight: 600;
            margin-bottom: 10px;
            opacity: 0.85;
            color: #f2f2f2;
        }

        p {
            font-size: 1rem;
            opacity: 0.55;
            margin-bottom: 32px;
            line-height: 1.6;
            color: #ccc;
        }

        a.btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            color: #000;
            padding: 15px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.3s;
        }

        a.btn:hover {
            transform: scale(1.06);
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
        }

        a.btn svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body>

    <div class="noise"></div>
    <div class="scan"></div>

    <main>
        <div class="card">
            <h1>404</h1>
            <h2>{{ __('Oops! Something went wrong.') }}</h2>
            <p>The page you’re looking for doesn’t exist or may have been moved.</p>

            <a href="/admin/dashboard" class="btn">
                <span>Back to Home</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                </svg>
            </a>
        </div>
    </main>

</body>

</html>
