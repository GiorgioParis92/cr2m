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
            The CRM URL has changed from <strong>crm.genius-market.fr</strong> to <strong>crm-atlas.fr</strong>.
        </div>
        <div class="countdown">
            You will be redirected in <span id="countdown">3</span> seconds.
        </div>
    </div>
</body>
</html>
