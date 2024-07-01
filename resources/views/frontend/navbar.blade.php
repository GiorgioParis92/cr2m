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
        <button class="navbar-toggler" type="button" 
            aria-expanded="false" aria-label="Toggle navigation">
            <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
            </div>
        </button>



        <div class="collapse navbar-collapse" id="main_nav">
            <ul class="navbar-nav">
                <li class="nav-item active"> 
                  <a class="nav-link" href="{{ route('dashboard') }}"><i class="fa fa-house"></i> Dashboard </a>
                </li>

                @if(auth()->user()->client_id==0 || auth()->user()->type_id==2)
                <li class="nav-item ">
                    <a class="nav-link" href="{{ route('beneficiaires.create') }}">
                        
                        <span class="sidenav-normal"><i class="fa fa-user-plus"></i>  Nouveau bénéficiaire </span>
                    </a>
                </li>
                @endif

                <li class="nav-item dropdown">
                    <a class="nav-link  dropdown-toggle" href="{{ route('dossiers.index') }}" data-bs-toggle="dropdown">
                      <i class="fa fa-folder-open"></i>  Dossiers </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('dossiers.index') }}"><i class="fa fa-list"></i>  Liste complete des
                                dossiers</a></li>

                    </ul>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="{{ route('rdvs') }}">
                        <span class="sidenav-normal"><i class="fa fa-calendar"></i>  Planning </span>
                    </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="{{ route('messagerie') }}">
                      <span class="sidenav-normal"><i class="fa fa-comments"></i>  Discussions </span>
                  </a>
              </li>

              @if (auth()->user()->type_id == 1)
                    <li class="nav-item">
                        <a class="nav-link  " href="{{ route('users.index') }}">

                            <span class="nav-link-text ms-1"><i class="fa fa-users"></i>  Utilisateurs</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user() && auth()->user()->client_id == 0)
                    <li class="nav-item dropdown">
                        <a class="nav-link  dropdown-toggle" data-bs-toggle="dropdown">
                          <i class="fa-solid fa-screwdriver-wrench"></i> Admin </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('clients.index') }}">
                                  <i class="fa-solid fa-users-line"></i>   Partenaires
                                </a>
                            </li>

                        </ul>
                    </li>


                  
                @endif


            </ul>
        </div> <!-- navbar-collapse.// -->

        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          <div class="input-group">
              <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" id="global" placeholder="Recherche...">
              <div style="display:none" id="search-results" class="container mt-3"></div>

          </div>
      </div>



      <ul class="navbar-nav  justify-content-end">
          <li class="nav-item d-flex align-items-center">
              <a class="nav-link text-body font-weight-bold px-0">

                  <span class="d-sm-inline "> <span class="btn btn-tertiary"><i
                              class="fa fa-user me-sm-1"></i>{{ auth()->user()->name }}
                          ({{ auth()->user()->type->type_desc ?? '' }})</span> <span
                          class="btn btn-secondary">{{ strtoupper(auth()->user()->client->client_title ?? '') }}</span></span>
              </a>
          </li>



          <li class="nav-item px-3 d-flex align-items-center d-none">
              <a href="javascript:;" class="nav-link text-body p-0">
                  <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
          </li>
          <li class="nav-item d-flex align-items-center">
              <a href="{{ route('logout') }}" class="nav-link text-body font-weight-bold px-0" style="    margin-left: 12px;">
                  <div class="d-sm-inline d-none logout ml-2"><i class="fa-solid fa-right-from-bracket btn btn-success p-2"></i></div>
              </a>
          </li>

      </ul>
    </div> <!-- container-fluid.// -->
</nav>
