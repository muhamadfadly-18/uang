@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <h2 class="fw-bold mb-0"><i class="bi bi-cash-stack"></i> Data Pengeluaran</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pengeluaranday.user.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Pengeluaran
            </a>

            <!-- Tombol Scan Struk -->
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalScanStruk">
                <i class="bi bi-camera"></i> Scan / Upload Struk
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body bg-white p-2 p-md-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th class="text-start">Keterangan</th>
                            <th>Harga per Item</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $d)
                        <tr class="text-center">
                            <td>{{ $i + 1 }}</td>
                            <td class="text-start">{{ $d->keterangan }}</td>
                            <td class="text-danger fw-semibold">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                            <td>{{ $d->jumlah }}</td>
                            <td class="fw-semibold">Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                            <td>{{ $d->created_at->format('d M Y') }}</td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    <a href="{{ route('pengeluaranday.user.edit', $d->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('pengeluaranday.user.destroy', $d->id) }}" method="POST" class="d-inline formHapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btnHapus">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada data pengeluaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                <p class="text-muted small mb-3">Gunakan kamera atau pilih foto dari galeri.</p>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                    <button id="btnOpenCamera" class="btn btn-outline-success">
                        <i class="bi bi-camera-video"></i> Gunakan Kamera
                    </button>
                    <label for="inputGallery" class="btn btn-outline-primary mb-0">
                        <i class="bi bi-image"></i> Pilih dari Galeri
                    </label>
                    <input type="file" id="inputGallery" accept="image/*" hidden>
                </div>

                <!-- Preview Kamera -->
                <div id="cameraContainer" class="camera-wrapper position-relative d-none mx-auto">
                    <video id="cameraPreview" autoplay playsinline class="rounded-4 border border-success w-100"></video>
                    <div class="scan-frame"></div>
                </div>

                <!-- Canvas -->
                <canvas id="cameraCapture" style="display:none;"></canvas>
            </div>

            <div class="modal-footer justify-content-between flex-wrap gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
                <button id="btnCapture" class="btn btn-success d-none">
                    <i class="bi bi-camera-fill"></i> Ambil Foto
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 1500
    });
    @endif

    // Konfirmasi hapus
    document.querySelectorAll('.btnHapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.formHapus');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => { if(result.isConfirmed) form.submit(); });
        });
    });

    // Kamera & Galeri
    let stream;
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('cameraCapture');
    const cameraContainer = document.getElementById('cameraContainer');
    const btnCapture = document.getElementById('btnCapture');
    const btnOpenCamera = document.getElementById('btnOpenCamera');
    const inputGallery = document.getElementById('inputGallery');

    btnOpenCamera.addEventListener('click', async () => {
        cameraContainer.classList.remove('d-none');
        btnCapture.classList.remove('d-none');
        try { stream = await navigator.mediaDevices.getUserMedia({video:{facingMode:"environment"}}); video.srcObject = stream; }
        catch(err){ Swal.fire('Gagal mengakses kamera', err.message,'error'); }
    });

    document.getElementById('modalScanStruk').addEventListener('hidden.bs.modal', () => {
        if(stream) stream.getTracks().forEach(track => track.stop());
        cameraContainer.classList.add('d-none');
        btnCapture.classList.add('d-none');
    });

    inputGallery.addEventListener('change', e => { if(e.target.files[0]) uploadImage(e.target.files[0]); });

    btnCapture.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(blob => uploadImage(blob),'image/png');
    });

    async function uploadImage(fileBlob){
        const formData = new FormData();
        formData.append('struk', fileBlob, 'scan.png');
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({title:'Memproses struk...',text:'Mohon tunggu sebentar',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});

        try{
            const response = await fetch('{{ route('pengeluaran.scan') }}',{method:'POST',body:formData});
            const result = await response.json();
            if(result.success){
                Swal.fire({icon:'success',title:'Berhasil Dibaca!',text:result.message||'Struk berhasil diproses!',timer:1500,showConfirmButton:false})
                    .then(()=>window.location.reload());
            }else Swal.fire('Gagal membaca struk',result.message||'Coba ulangi.','error');
        }catch(err){ Swal.fire('Error','Terjadi kesalahan saat memproses struk.','error'); }
    }
});
</script>

<style>
body{background-color:#f8f9fa;}
.card{border-radius:15px;}
.table{border-radius:10px;overflow:hidden;min-width:600px;}
.table th{font-weight:600;}
.table td{vertical-align:middle;}
.btn{border-radius:10px;}
.modal-content{border-radius:15px;}
.camera-wrapper{position:relative;width:100%;max-width:500px;aspect-ratio:3/4;overflow:hidden;margin:auto;}
#cameraPreview{width:100%;height:100%;object-fit:cover;}
.scan-frame{position:absolute;top:15%;left:10%;width:80%;height:60%;border:3px solid #00ff00;border-radius:12px;box-shadow:0 0 20px rgba(0,255,0,0.4);animation:pulse 2s infinite;}
@keyframes pulse{0%{box-shadow:0 0 20px rgba(0,255,0,0.4);}50%{box-shadow:0 0 40px rgba(0,255,0,0.8);}100%{box-shadow:0 0 20px rgba(0,255,0,0.4);}}
@media(max-width:768px){.table-responsive{overflow-x:auto;}.text-nowrap{white-space:nowrap;}.d-flex.flex-column.flex-md-row{flex-direction:column!important;}.flex-wrap{flex-wrap:wrap!important;}}
</style>
@endsection
