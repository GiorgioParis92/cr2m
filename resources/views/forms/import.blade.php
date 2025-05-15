@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Importer un fichier Excel</h3>
    <form action="{{ route('forms.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="excel" class="form-label">Fichier Excel</label>
            <input type="file" name="excel" class="form-control" required accept=".xlsx,.xls">
        </div>
        <button type="submit" class="btn btn-primary">Importer</button>
    </form>
</div>
@endsection
