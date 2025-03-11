<!DOCTYPE html>
<html>
<head>
    <title>PDF Template</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        .content {
            width: 100%;
            height: 100%;
        }
        img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Ensures the image fits the page without distortion */
        }
    </style>
</head>
<body>
    <div class="content">
        <img src="{{ $imagePath }}" alt="Image for PDF">
    </div>
</body>
</html>
