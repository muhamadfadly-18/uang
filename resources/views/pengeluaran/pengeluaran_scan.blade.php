@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <h2 class="fw-bold mb-0"><i class="bi bi-cash-stack"></i> Data Pengeluaran</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pengeluaranday.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Pengeluaran
            </a>

            <!-- Tombol Scan Struk -->
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalScanStruk">
                <i class="bi bi-camera"></i> Scan / Upload Struk
            </button>
        </div>
    </div>
</div>

<!-- Modal Scan Struk -->
<div class="modal fade" id="modalScanStruk" tabindex="-1" aria-labelledby="modalScanStrukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalScanStrukLabel"><i class="bi bi-camera"></i> Scan atau Upload Struk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="text-muted small mb-3">Arahkan struk ke kotak hijau untuk auto scan.</p>

                <div id="cameraContainer" class="camera-wrapper position-relative mx-auto">
                    <video id="cameraPreview" autoplay playsinline class="rounded-4 border border-success w-100"></video>
                    <div class="scan-frame"></div>
                </div>

                <canvas id="cameraCapture" style="display:none;"></canvas>
            </div>

            <div class="modal-footer justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Tesseract.js -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('cameraCapture');
    const cameraContainer = document.getElementById('cameraContainer');
    const scanFrame = document.querySelector('.scan-frame');
    const btnOpenCamera = document.querySelector('[data-bs-target="#modalScanStruk"]');

    let stream;

    btnOpenCamera.addEventListener('click', async () => {
        cameraContainer.classList.remove('d-none');

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
            video.srcObject = stream;

            const worker = Tesseract.createWorker({ logger: m => console.log(m) });
            await worker.load();
            await worker.loadLanguage('eng');
            await worker.initialize('eng');

            const autoScan = setInterval(async () => {
                const frameRect = scanFrame.getBoundingClientRect();
                const videoRect = video.getBoundingClientRect();
                const scaleX = video.videoWidth / videoRect.width;
                const scaleY = video.videoHeight / videoRect.height;

                const cropX = (frameRect.left - videoRect.left) * scaleX;
                const cropY = (frameRect.top - videoRect.top) * scaleY;
                const cropWidth = frameRect.width * scaleX;
                const cropHeight = frameRect.height * scaleY;

                canvas.width = cropWidth;
                canvas.height = cropHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);

                const { data: { text } } = await worker.recognize(canvas);
                if(text.trim()){ 
                    console.log('Teks terdeteksi:', text);
                    Swal.fire({
                        icon: 'success',
                        title: 'Struk terbaca!',
                        html: `<pre>${text}</pre>`,
                        showConfirmButton: true
                    });
                    clearInterval(autoScan);
                    await worker.terminate();
                    if(stream) stream.getTracks().forEach(track => track.stop());
                    cameraContainer.classList.add('d-none');
                }
            }, 2000);

            document.getElementById('modalScanStruk').addEventListener('hidden.bs.modal', () => {
                clearInterval(autoScan);
                if(stream) stream.getTracks().forEach(track => track.stop());
                cameraContainer.classList.add('d-none');
            });

        } catch(err){
            Swal.fire('Gagal mengakses kamera', err.message, 'error');
        }
    });
});
</script>

<style>
.camera-wrapper {
    position: relative;
    width: 100%;
    max-width: 500px;
    aspect-ratio: 3/4;
    overflow: hidden;
    margin: auto;
}
#cameraPreview { width: 100%; height: 100%; object-fit: cover; }
.scan-frame {
    position: absolute;
    top: 15%; left: 10%;
    width: 80%; height: 60%;
    border: 3px solid #00ff00;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0,255,0,0.4);
    animation: pulse 2s infinite;
}
@keyframes pulse{
    0% { box-shadow: 0 0 20px rgba(0,255,0,0.4); }
    50% { box-shadow: 0 0 40px rgba(0,255,0,0.8); }
    100% { box-shadow: 0 0 20px rgba(0,255,0,0.4); }
}
</style>
@endsection
