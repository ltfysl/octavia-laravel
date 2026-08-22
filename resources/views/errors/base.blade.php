<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — Octavia</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|space-grotesk:500,600,700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #fbfbfa; color: #101018; min-height: 100vh; display: flex; flex-direction: column; }
        .wrap { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; text-align: center; }
        .logo { display: inline-flex; align-items: center; gap: .625rem; text-decoration: none; color: #101018; margin-bottom: 3rem; }
        .tile { width: 2rem; height: 2rem; border-radius: .5rem; background: #5f4bd8; color: #fff; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.125rem; }
        .name { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.125rem; letter-spacing: -0.02em; }
        .code { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: clamp(4rem, 12vw, 7rem); line-height: 1; color: #5f4bd8; margin-bottom: .5rem; }
        h1 { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 1.5rem; letter-spacing: -0.02em; margin-bottom: .75rem; }
        p { color: #6b6b85; max-width: 28rem; margin-bottom: 2rem; line-height: 1.6; }
        a.btn { display: inline-block; background: #5f4bd8; color: #fff; padding: .75rem 1.5rem; border-radius: .5rem; text-decoration: none; font-weight: 500; font-size: .9rem; transition: background 150ms; }
        a.btn:hover { background: #4d3bb8; }
    </style>
</head>
<body>
<div class="wrap">
    <a href="/" class="logo"><span class="tile">O</span><span class="name">Octavia</span></a>
    <div class="code">@yield('code')</div>
    <h1>@yield('title')</h1>
    <p>@yield('message')</p>
    <a href="/" class="btn">Back to home</a>
</div>
</body>
</html>
