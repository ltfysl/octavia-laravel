<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — Octavia</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://fonts.bunny.net/css?family=archivo:500,600,700|ibm-plex-sans:400,500,600|jetbrains-mono:400,500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'IBM Plex Sans', system-ui, sans-serif; background: #fafbfb; color: #0e1a1d; min-height: 100vh; display: flex; flex-direction: column; }
        .wrap { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; text-align: center; position: relative; }
        .terrain { position: absolute; inset: 0; pointer-events: none; }
        .logo { position: relative; display: inline-flex; align-items: center; gap: .625rem; text-decoration: none; color: #0e1a1d; margin-bottom: 3rem; }
        .tile { width: 2rem; height: 2rem; border-radius: .375rem; background: #ea580c; color: #0e1a1d; display: flex; align-items: center; justify-content: center; font-family: 'Archivo', sans-serif; font-weight: 700; font-size: 1.125rem; }
        .name { font-family: 'Archivo', sans-serif; font-weight: 600; font-size: 1.125rem; letter-spacing: -0.02em; }
        .eyebrow { font-family: 'Archivo', sans-serif; font-weight: 600; font-size: .6875rem; letter-spacing: .14em; text-transform: uppercase; color: #9fb2b6; margin-bottom: .75rem; }
        .code { position: relative; font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: clamp(4rem, 12vw, 7rem); line-height: 1; color: #ea580c; margin-bottom: .5rem; }
        h1 { font-family: 'Archivo', sans-serif; font-weight: 700; font-size: 1.5rem; letter-spacing: -0.02em; margin-bottom: .75rem; }
        p { color: #5c7076; max-width: 28rem; margin-bottom: 2rem; line-height: 1.6; }
        a.btn { display: inline-block; background: #0e1a1d; color: #fff; padding: .75rem 1.5rem; border-radius: .375rem; text-decoration: none; font-weight: 500; font-size: .9rem; transition: background 150ms; }
        a.btn:hover { background: #2f4449; }
        @media (prefers-reduced-motion: no-preference) {
            .code { animation: settle 600ms ease-out both; }
            @keyframes settle { from { opacity: 0; transform: translateY(.5rem); } to { opacity: 1; transform: none; } }
        }
    </style>
</head>
<body>
<div class="wrap">
    <svg class="terrain" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice" fill="none" aria-hidden="true">
        <path d="M-20 380 C140 356 240 396 400 372 S680 320 860 344" stroke="#e3ebec"/>
        <path d="M-20 320 C160 296 280 336 440 312 S720 264 880 288" stroke="#e3ebec" opacity=".7"/>
        <path d="M40 250 C200 230 320 262 480 240 S760 196 880 216" stroke="#e3ebec" opacity=".45"/>
        <path d="M120 180 C260 162 360 190 520 170 S780 132 880 148" stroke="#e3ebec" opacity=".25"/>
        <path d="M-20 430 C160 410 300 442 480 420 S780 386 900 404" stroke="#ffc7a3" stroke-dasharray="4 5"/>
    </svg>
    <a href="/" class="logo"><span class="tile">O</span><span class="name">Octavia</span></a>
    <div class="code">@yield('code')</div>
    <h1>@yield('title')</h1>
    <p>@yield('message')</p>
    <a href="/" class="btn">Back to home</a>
</div>
</body>
</html>
