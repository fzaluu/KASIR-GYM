<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | Virgo Gym</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .error-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 16px;
            padding: 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #ffffff;
        }
        p {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.6;
            margin: 0 0 24px 0;
        }
        .btn-dashboard {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-dashboard:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        .brand {
            margin-top: 24px;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-box">
            <!-- Ikon Gembok / Kunci -->
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1>Akses Ditolak</h1>
        <p>Kamu tidak memiliki izin untuk mengakses halaman ini. Hubungi admin kalau menurutmu ini keliru.</p>
        <a href="{{ url('/') }}" class="btn-dashboard" id="retryLink">Kembali ke Dashboard</a>
        <div class="brand">Virgo Gym Security System</div>
    </div>
</body>
</html>
<script>
    const retryLink = document.getElementById('retryLink');
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