<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>leCaptcha Widget</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { 
            margin: 0; 
            padding: 8px; 
            font-family: 'Lato', sans-serif; 
            background: transparent;
        }
    </style>
</head>
<body>
    @yield('content')
    
    <script>
        function sendMessageToParent(data) {
            if (window.parent !== window) {
                window.parent.postMessage(data, '*');
            }
        }
        
        function notifyValidation(success, message) {
            sendMessageToParent({
                type: 'captcha_validated',
                success: success,
                message: message
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>