<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('dashboard') }}" target="_blank">

                @if (auth()->user() && auth()->user()->client_id > 0 && isset($client->main_logo))
                    @if (Storage::disk('public')->exists($client->main_logo))
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $client->main_logo) }}" class="navbar-brand-img h-100"
                                alt="main_logo">
                            <br /><span class="ms-1 font-weight-bold">{{ $client->client_title }}</span>
                        </div>
                    @endif
                @else
                    <div class="text-center">
                        <img src="{{ asset('frontend/assets/img/logo genius.png') }}" class="navbar-brand-img h-100"
                            alt="main_logo"><br />
                    </div>
                @endif

            </a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
              <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
              </div>
            </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav">
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#dossiers" class="nav-link "
                    aria-controls="dossiers" role="button" aria-expanded="false">
                   
                    <span class="nav-link-text ms-1">Dossiers</span>
                </a>
                <div class="collapse " id="dossiers">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('dossiers.index')}}">
                                <span class="sidenav-mini-icon"> L </span>
                                <span class="sidenav-normal"> Liste des dossiers </span>
                            </a>
                        </li>
               
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#beneficiaires" class="nav-link "
                    aria-controls="beneficiaires" role="button" aria-expanded="false">
                 
                    <span class="nav-link-text ms-1">Bénéficiaires</span>
                </a>
                <div class="collapse " id="beneficiaires">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('beneficiaires.index')}}">
                                <span class="sidenav-mini-icon"> B </span>
                                <span class="sidenav-normal"> Liste des bénéficiaires </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('beneficiaires.create')}}">
                                <span class="sidenav-mini-icon"> B </span>
                                <span class="sidenav-normal"> Nouveau bénéficiaire </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
           
            <li class="nav-item">
                <a class="nav-link  " href="{{ route('clients.index') }}">
                   
                    <span class="nav-link-text ms-1">MAR/INSTALLATEURS</span>
                </a>
            </li>
           

            <li class="nav-item">
                <a class="nav-link  " href="{{ route('users.index') }}">
    
                    <span class="nav-link-text ms-1">Utilisateurs</span>
                </a>
            </li>
           

         
        </ul>
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
              <div class="input-group">
                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="Type here...">
              </div>
            </div>
          
            
            <ul class="navbar-nav  justify-content-end">
              <li class="nav-item d-flex align-items-center">
                <a  class="nav-link text-body font-weight-bold px-0">
                  
                  <span class="d-sm-inline d-none"> <span class="btn btn-tertiary"><i class="fa fa-user me-sm-1"></i>{{auth()->user()->name}} ({{auth()->user()->type->type_desc ?? ''}})</span> <span class="btn btn-secondary">{{strtoupper(auth()->user()->client->client_title ?? '')}}</span></span>
                </a>
              </li>
  
  
   
              <li class="nav-item px-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0">
                  <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                </a>
              </li>
              <li class="nav-item d-flex align-items-center">
                <a href="{{ route('logout') }}" class="nav-link text-body font-weight-bold px-0">
                  <span class="d-sm-inline d-none">Logout</span>
                </a>
              </li>
              
            </ul>
        </div>
    </div>
</nav>
