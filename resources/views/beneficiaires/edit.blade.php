@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Beneficiaire</h4>
    <form id="beneficiaireForm">
        @csrf
        @method('PUT')
        @include('beneficiaires.partials.form', ['beneficiaire' => $beneficiaire, 'isCreate' => false])
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    <div id="successMessage" class="alert alert-success" style="display:none;"></div>
    <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#beneficiaireForm').on('submit', function(event) {
        event.preventDefault();
        
        let formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route('beneficiaires.update', $beneficiaire->id) }}',
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#successMessage').text(response.success).show();
                $('#errorMessage').hide();
                setTimeout(function() {
                    window.location.href = '{{ route('beneficiaires.index') }}';
                }, 2000); // Redirect after 2 seconds
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }
                $('#errorMessage').text(errorMessage).show();
                $('#successMessage').hide();
            }
        });
    });
});
</script>
@endsection
