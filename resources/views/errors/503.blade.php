<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode — We'll Be Back Soon</title>
    <!-- Phosphor Icons for UI Elements -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Outfit', sans-serif;
            background-color: #0b0f19;
            color: #f3f4f6;
            overflow: hidden;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            position: relative;
        }

        /* Ambient Glow Blobs */
        .glow-blob {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.15) 0%, rgba(0,0,0,0) 70%);
            filter: blur(40px);
            z-index: 0;
            pointer-events: none;
        }
        .glow-1 {
            top: -10%;
            left: -10%;
        }
        .glow-2 {
            bottom: -10%;
            right: -10%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(0,0,0,0) 70%);
        }

        /* Container & Glassmorphism Card */
        .card {
            position: relative;
            z-index: 10;
            max-width: 540px;
            width: 100%;
            padding: 48px 32px;
            background: rgba(17, 24, 39, 0.7);
            border-radius: 28px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header Illustration */
        .icon-container {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.1) 0%, rgba(225, 29, 72, 0.2) 100%);
            border: 1px solid rgba(225, 29, 72, 0.25);
            color: #e11d48;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(225, 29, 72, 0.15);
        }

        .icon-container i {
            font-size: 36px;
            animation: pulse-gear 6s linear infinite;
        }

        @keyframes pulse-gear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: clamp(24px, 4.5vw, 36px);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #ffffff 40%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 0.95rem;
            color: #9ca3af;
            line-height: 1.6;
            margin-bottom: 28px;
            max-width: 440px;
        }

        /* Countdown Cards Grid */
        .countdown-grid {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
            width: 100%;
        }

        .countdown-item {
            background: rgba(31, 41, 55, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 10px;
            min-width: 68px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .countdown-value {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .countdown-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #e11d48;
            margin-top: 2px;
        }

        /* Form Controls */
        .admin-access-form {
            width: 100%;
            max-width: 360px;
            margin-top: 8px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(10, 15, 26, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 4px 4px 4px 16px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .input-group:focus-within {
            border-color: rgba(225, 29, 72, 0.5);
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.15);
            background: rgba(10, 15, 26, 0.7);
        }

        .input-group i {
            color: #6b7280;
            font-size: 18px;
            margin-right: 10px;
            transition: color 0.25s;
        }

        .input-group:focus-within i {
            color: #e11d48;
        }

        .input-group input {
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 0.9rem;
            font-family: inherit;
            width: 100%;
            height: 40px;
        }

        .input-group input::placeholder {
            color: #6b7280;
        }

        /* Submit Button */
        .btn-submit {
            background: #e11d48;
            color: #ffffff;
            border: none;
            outline: none;
            border-radius: 12px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
        }

        .btn-submit:hover {
            background: #be123c;
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .card {
                padding: 36px 20px;
            }
            h1 {
                font-size: 24px;
            }
            p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
@php
    $data = getOptionWithJsonDecode('maintenance_mode', []);
@endphp

<body style="background-image: radial-gradient(circle at 50% 50%, rgba(11, 15, 25, 0.93) 0%, rgba(3, 7, 18, 0.98) 100%), url('{{ data_get($data, 'image', '/assets/maintenance.png') }}'); background-size: cover; background-position: center;">
    
    <!-- Ambient Glow Background -->
    <div class="glow-blob glow-1"></div>
    <div class="glow-blob glow-2"></div>

    <div class="card">
        {{-- Gear Icon Badge --}}
        <div class="icon-container">
            <i class="ph ph-gear"></i>
        </div>

        <h1>We’ll Be Back Soon!</h1>
        <p>{{ data_get($data, 'description', 'Our site is currently undergoing scheduled maintenance. Please check back shortly!') }}</p>

        @if (data_get($data, 'countdown', false))
            <div class="countdown-grid" id="countdown-wrapper">
                <div class="countdown-item">
                    <div class="countdown-value" id="c-days">00</div>
                    <div class="countdown-label">Days</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="c-hours">00</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="c-minutes">00</div>
                    <div class="countdown-label">Mins</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="c-seconds">00</div>
                    <div class="countdown-label">Secs</div>
                </div>
            </div>

            <script>
                const countDownDate = new Date("{{ data_get($data, 'countdown', now()->addMinutes(30)) }}").getTime();
                const x = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("c-days").innerText = String(days).padStart(2, '0');
                    document.getElementById("c-hours").innerText = String(hours).padStart(2, '0');
                    document.getElementById("c-minutes").innerText = String(minutes).padStart(2, '0');
                    document.getElementById("c-seconds").innerText = String(seconds).padStart(2, '0');

                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("countdown-wrapper").innerHTML = "<div class='text-emerald-500 font-bold text-sm uppercase tracking-wider'>We are back online! Refreshing...</div>";
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                }, 1000);
            </script>
        @endif

        {{-- Unified Admin Secret Key Field --}}
        <div class="admin-access-form">
            <div class="input-group">
                <i class="ph ph-lock-key"></i>
                <input type="password" id="secret_key" placeholder="Enter admin secret key" autocomplete="off" />
                <a href="javascript:void(0)" id="admin_login" class="btn-submit">
                    <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const secretInput = document.getElementById('secret_key');
            const loginBtn = document.getElementById('admin_login');
            const BASE_URL = "{{ request()->root() }}";

            secretInput.addEventListener('input', () => {
                const value = secretInput.value.trim();
                if (!value) {
                    resetState();
                    return;
                }
                loginBtn.href = `${BASE_URL}/${value}`;
            });

            secretInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const value = secretInput.value.trim();
                    if (value) {
                        window.location.href = `${BASE_URL}/${value}`;
                    }
                }
            });

            function resetState() {
                loginBtn.href = "javascript:void(0)";
            }
        })();
    </script>
</body>

</html>
