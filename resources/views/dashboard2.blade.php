@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <h1>Dashboard 2</h1>
        <p>Welcome to Dashboard 2, only accessible by users with a valid client ID.</p>
    </div>

    <div class="row">
        @foreach($campagnes as $campagne)
        <div class="card col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card-body d-flex flex-row">
              <img src="https://mdbootstrap.com/img/Photos/Avatars/avatar-8.jpg" class="rounded-circle me-3" height="50px"
                width="50px" alt="avatar" />
              <div>
                <h5 class="card-title font-weight-bold mb-2">{{$campagne->campagne_title}}</h5>
                <p class="card-text"><i class="far fa-clock pe-2"></i>Campagne créée le {{date('d/m/Y',strtotime($campagne->created_at))}}</p>
              </div>
            </div>
            <div class="bg-image hover-overlay ripple rounded-0" data-mdb-ripple-color="light">
              <img class="img-fluid" 
              src="{{$campagne->main_image ? asset('storage/uploads/' . basename($campagne->main_image))  : 'https://mdbootstrap.com/img/Photos/Horizontal/Food/full page/2.jpg'}}"
                alt="Card image cap" />
             
            </div>
            <div class="card-body">
              <p class="card-text collapse" id="collapseContent">
                Recently, we added several exotic new dishes to our restaurant menu. They come from
                countries such as Mexico, Argentina, and Spain. Come to us, have some wine and enjoy
                our juicy meals from around the world.
              </p>
              <div class="d-flex justify-content-between">
                <a class="btn btn-link link-danger p-md-1 my-1" data-mdb-toggle="collapse" href="#collapseContent"
                  role="button" aria-expanded="false" aria-controls="collapseContent">{{$campagne->status->status_desc}}
                </a>
                <a>{{count($campagne->docs)}} photos</a>
                <div>
                  {{-- <i class="fas fa-share-alt text-muted p-md-1 my-1 me-2" data-mdb-toggle="tooltip"
                    data-mdb-placement="top" title="Share this post"></i> --}}
                    @if($user->client_id==0)
                    <a href="{{ route('campagnes.edit', ['id' => $campagne->campagne_id]) }}">
                  <i class="fas fa-heart text-muted p-md-1 my-1 me-0" data-mdb-toggle="tooltip" data-mdb-placement="top"
                    title="Edit"></i>
                    </a>
                    @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
    </div>
</div>
@endsection