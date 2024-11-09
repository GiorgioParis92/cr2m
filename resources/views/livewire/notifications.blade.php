<div>
    <script>
        const iconPath = '{{ asset('logo.PNG') }}'

        Push.create("Hello Shailesh!", {
            body: "Welcome to the Dashboard.",
            timeout: 5000,
            icon: iconPath
        });
    </script>
    
</div>
