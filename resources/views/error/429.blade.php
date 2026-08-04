<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Percobaan</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e3a8a, #0ea5e9);
            background-size: 400% 400%;
            animation: bgMove 12s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        @keyframes bgMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrap svg {
            width: 32px;
            height: 32px;
            stroke: #fca5a5;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0 0 8px;
            color: #ffffff;
        }

        p.desc {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            margin: 0 0 24px;
        }

        .countdown-box {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            padding: 20px 16px;
            margin-bottom: 24px;
        }

        #countdown {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fca5a5;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .countdown-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .progress-track {
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            overflow: hidden;
            margin-top: 14px;
        }

        .progress-fill {
            height: 100%;
            width: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, #f87171, #dc2626);
            transition: width 1s linear;
        }

        a#retryLink {
            display: none;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: white;
            cursor: pointer;
            background: linear-gradient(135deg, #38bdf8, #2563eb);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            box-sizing: border-box;
        }

        a#retryLink:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        .fade-in { animation: fadeUp 0.5s ease-out; }
        .a.loading {
        pointer-events: none;
        background: #2563eb;
        opacity: 0.85;
    }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
    </div>

    <h1>Terlalu Banyak Percobaan</h1>
    <p class="desc">
        Kamu sudah mencoba login terlalu sering. Demi keamanan akun,
        silakan tunggu sebentar sebelum mencoba lagi.
    </p>

    <div class="countdown-box">
        <div id="countdown">--</div>
        <div class="countdown-label">Coba lagi dalam</div>
        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </div>

    <a href="{{ url('/login') }}" id="retryLink">Coba Login Lagi</a>
</div>

<script>
    const totalSeconds = {{ isset($exception) && method_exists($exception, 'getHeaders') && isset($exception->getHeaders()['Retry-After']) ? $exception->getHeaders()['Retry-After'] : 60 }};
    let seconds = totalSeconds;

    const countdownEl = document.getElementById('countdown');
    const retryLink = document.getElementById('retryLink');
    const progressFill = document.getElementById('progressFill');
    const countdownBox = document.querySelector('.countdown-box');

    countdownEl.textContent = seconds + ' detik';

    const timer = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
            clearInterval(timer);
            countdownEl.textContent = '✓';
            document.querySelector('.countdown-label').textContent = 'Kamu sudah bisa mencoba lagi';
            progressFill.style.width = '0%';
            countdownBox.classList.add('fade-in');
            retryLink.style.display = 'inline-block';
            retryLink.classList.add('fade-in');
        } else {
            countdownEl.textContent = seconds + ' detik';
            progressFill.style.width = (seconds / totalSeconds * 100) + '%';
        }
    }, 1000);

    // Perbaikan event listener pada tombol retryLink
    if (retryLink) {
        retryLink.addEventListener("click", function(e){
            this.classList.add("loading");
            this.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 50 50" style="vertical-align:middle;margin-right:8px;">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" stroke-dasharray="31.4 31.4">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.8s" values="0 25 25;360 25 25"/>
                    </circle>
                </svg>
                Memproses...
            `;
        });
    }
</script>
</body>
</html>