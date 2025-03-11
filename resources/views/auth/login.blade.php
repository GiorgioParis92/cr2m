@if (request()->getHost() === 'crm-atlas.fr' 
// || env('APP_ENV') === 'local'
) 


<!-- resources/views/url-change-notification.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM URL Change Notification</title>
    <meta http-equiv="refresh" content="3;url=https://crm-atlas.fr">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .message {
            font-size: 18px;
            color: #333;
        }
        .countdown {
            font-size: 16px;
            color: #777;
        }
    </style>
    <script>
        let countdown = 3;
        function updateCountdown() {
            if (countdown > 0) {
                document.getElementById('countdown').innerText = countdown;
                countdown--;
            }
        }
        setInterval(updateCountdown, 1000);
    </script>
</head>
<body>
    <div class="container">
        <div class="message">
            L'adresse du CRM a changé <strong>crm.genius-market.fr</strong> to <strong>crm-atlas.fr</strong>.

            Veuillez dorénavant vous connecter via : <a href="https://crm-atlas.fr">https://crm-atlas.fr</a>
        </div>
        <div class="countdown">
            Vous allez être redirigé dans <span id="countdown">3</span> secondes.
        </div>
    </div>
</body>
</html>



@else 

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('frontend/assets/img/favicon.ico') }}">
  <title>
CRM ATLAS
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('frontend/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link href="{{ asset('frontend/assets/css/custom_css.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('frontend/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('frontend/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
</head>

<body class="">
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        {{-- <h2 class="text-center text-dark mt-5">Login Form</h2>
        <div class="text-center mb-5 text-dark">Made with bootstrap</div> --}}
        <div class="card my-5">

          <form class="card-body cardbody-color p-lg-5" role="form" method="POST" action="{{ route('login') }}">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="text-center">
              <img src="{{ asset('storage/images/atlas_noir.png') }}" class=""
                width="200px" alt="profile">
            </div>

            <div class="mb-3">
              <input name="email" type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon">

            </div>
            <div class="mb-3">
              <input name="password" type="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
            </div>
            <div class="text-center"><button type="submit" class="btn btn-color px-5 mb-5 w-100">Login</button></div>
            {{-- <div id="emailHelp" class="form-text text-center mb-5 text-dark">Not
              Registered? <a href="#" class="text-dark fw-bold"> Create an
                Account</a> --}}
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <style>
    .btn-color{
  background-color: #0e1c36;
  color: #fff;
  
}

.profile-image-pic{
  height: 200px;
  width: 200px;
  object-fit: cover;
}



.cardbody-color{
  background-color: #ebf2fa;
}

a{
  text-decoration: none;
}
</style>
</body>

</html>


@endif
