<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We'll Be Back Soon</title>
    <style>
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
            background-color: #0a0a0a;
            color: #fff;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background: #0a0a0a no-repeat center center;
            background-size: cover;
            position: relative;
        }

        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            padding: 40px 20px;
            background: rgba(15, 15, 15, 0.6);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        h1 {
            font-size: clamp(36px, 5vw, 60px);
            font-weight: 800;
            margin-bottom: 20px;
            color: #facc15;
            background: linear-gradient(90deg, #facc15, #f97316, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1rem;
            margin-bottom: 30px;
            color: #ccc;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 50px;
            text-decoration: none;
            color: #000;
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        #countdown {
            font-size: 1.8rem;
            font-weight: 600;
            margin-top: 20px;
            color: #facc15;
        }

        .admin-access {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 25px;
        }

        .admin-access input {
            padding: 14px 18px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .admin-access input::placeholder {
            color: #888;
        }

        .admin-access input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }

        .admin-access .btn {
            opacity: 0.5;
            pointer-events: none;
            cursor: not-allowed;
        }

        .admin-access .btn.active {
            opacity: 1;
            pointer-events: auto;
            cursor: pointer;
        }

        #key_status {
            font-size: 0.85rem;
            min-height: 18px;
        }

        #key_status.success {
            color: #22c55e;
        }

        #key_status.error {
            color: #ef4444;
        }


        @media (max-width: 600px) {
            h1 {
                font-size: clamp(28px, 10vw, 48px);
            }

            p {
                font-size: 0.95rem;
            }

            .btn {
                padding: 10px 22px;
                font-size: 0.9rem;
            }

            #countdown {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
@php
    $data = getOptionWithJsonDecode('maintenance_mode', []);
@endphp

<body style="background-image: url('{{ data_get($data, 'image', '/assets/maintenance.png') }}');">
    <div class="container">
        <h1>We’ll Be Back Soon!</h1>
        <p>{{ data_get($data, 'description', 'Our site is currently undergoing maintenance. Please check back later!') }}
        </p>
        <div class="admin-access">
            <input type="password" id="secret_key" placeholder="Enter admin secret key" autocomplete="off" />

            <a href="javascript:void(0)" id="admin_login" class="btn active">
                Admin Login
            </a>
        </div>
        @if (data_get($data, 'countdown', false))
            <div id="countdown"></div>
            <script>
                const countDownDate = new Date("{{ data_get($data, 'countdown', now()->addMinutes(30)) }}").getTime();
                const x = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("countdown").innerHTML =
                        (days > 0 ? days + "d " : "") + hours + "h " + minutes + "m " + seconds + "s";
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("countdown").innerHTML = "We're back!";
                    }
                }, 1000);
            </script>
        @endif
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

            function resetState() {
                loginBtn.href = "javascript:void(0)";
            }

        })();
    </script>

</body>

</html>
