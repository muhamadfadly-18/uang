@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Scan / Upload Struk</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('pengeluaran.scan') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="struk" class="form-label">Pilih Foto Struk</label>
            <input type="file" name="struk" id="struk" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Upload dan Scan</button>
    </form>
</div>
@endsection
