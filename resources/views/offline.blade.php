<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KinderPay - Offline</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 0 1rem;
            text-align: center;
        }
        .container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 28rem;
            width: 100%;
        }
        .logo {
            color: #4f46e5;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
            font-size: 1rem;
        }
        button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">KinderPay</div>
        <h1>You're Offline</h1>
        <p>You are currently offline. Please check your internet connection.</p>
        <button onclick="window.location.reload()">Retry Connection</button>
    </div>
</body>
</html>
