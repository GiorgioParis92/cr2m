@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Nouveau dossier</h4>
    
    <div id="successMessage" class="alert alert-success" style="display:none;"></div>
    <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>

    <form id="beneficiaireForm" action="{{route('beneficiaires.store')}}" method="post">
        <div class="row">
        @csrf
        @include('beneficiaires.partials.form', ['beneficiaire' => new App\Models\Beneficiaire, 'isCreate' => true])
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>

</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // $('#beneficiaireForm').on('submit', function(event) {
    //     event.preventDefault();
        
    //     let formData = $(this).serialize();
        
    //     $.ajax({
    //         url: '{{ route('beneficiaires.store') }}',
    //         method: 'POST',
    //         data: formData,
    //         success: function(response) {
    //             $('#successMessage').text(response.success).show();
    //             $('#errorMessage').hide();
    //             $('#beneficiaireForm')[0].reset();
    //         },
    //         error: function(xhr) {
    //             let errorMessage = 'An error occurred';
    //             if (xhr.responseJSON && xhr.responseJSON.errors) {
    //                 errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
    //             }
    //             $('#errorMessage').text(errorMessage).show();
    //             $('#successMessage').hide();
    //         }
    //     });
    // });
});
</script>
@endsection
