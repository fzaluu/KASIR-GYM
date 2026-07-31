<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Member Gym</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Html5-Qrcode Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body class="bg-slate-900 flex flex-col items-center justify-center min-h-screen text-white">

    <div class="bg-slate-800 p-8 rounded-2xl shadow-2xl w-full max-w-md text-center border border-slate-700">
        <h1 class="text-2xl font-bold mb-2 text-emerald-400">Gym Check-in Scanner</h1>
        <p class="text-sm text-slate-400 mb-6">Arahkan QR Code kartu member ke depan kamera</p>

        <!-- Kotak Kamera -->
        <div id="reader" class="w-full rounded-xl overflow-hidden bg-slate-900 border-2 border-dashed border-slate-600"></div>
        
        <div class="mt-6">
            <a href="{{ route('member.index') }}" class="inline-block bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-semibold py-2.5 px-5 rounded-lg transition">
                &larr; Kembali ke Data Member
            </a>
        </div>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.clear();

            // Mengarahkan ke rute proses check-in QR yang benar
            fetch('/proses-checkin-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ member_id: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Check-in Berhasil! 🎉',
                        html: `<strong class="text-lg">${data.nama}</strong><br><span class="text-sm text-gray-400">${data.message}</span>`,
                        timer: 3500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check-in Gagal ❌',
                        text: data.message,
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error').then(() => {
                    location.reload();
                });
            });
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>