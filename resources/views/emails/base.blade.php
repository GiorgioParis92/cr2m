<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        /* Add any common styles here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin: 0 auto;
            max-width: 600px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} CRM-ATLAS. All rights reserved.
    </div>
</body>
</html>
