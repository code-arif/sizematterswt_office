<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ $page->title ?? 'Privacy Policy' }} | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $page->meta_description ?? 'Privacy Policy page' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/default/favicon.png') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0d6efd;
            --primary-light: #4d94ff;
            --primary-dark: #0a58ca;
            --accent: #00c9a7;
            --bg-gradient-start: #0a1628;
            --bg-gradient-mid: #1a2942;
            --bg-gradient-end: #0f1b33;
            --text-light: #f8f9fa;
            --text-muted: rgba(255, 255, 255, 0.7);
            --card-bg: rgba(255, 255, 255, 0.06);
            --card-border: rgba(255, 255, 255, 0.1);
            --card-hover: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-mid) 50%, var(--bg-gradient-end) 100%);
            color: var(--text-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Animated background particles ── */
        .bg-particles {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .bg-particles span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(13, 110, 253, 0.12);
            border-radius: 50%;
            animation: float 20s infinite;
            bottom: -150px;
        }

        .bg-particles span:nth-child(1) {
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .bg-particles span:nth-child(2) {
            left: 20%;
            width: 40px;
            height: 40px;
            animation-delay: 2s;
            animation-duration: 22s;
        }

        .bg-particles span:nth-child(3) {
            left: 35%;
            width: 60px;
            height: 60px;
            animation-delay: 4s;
            animation-duration: 20s;
        }

        .bg-particles span:nth-child(4) {
            left: 50%;
            width: 30px;
            height: 30px;
            animation-delay: 0s;
            animation-duration: 25s;
        }

        .bg-particles span:nth-child(5) {
            left: 65%;
            width: 50px;
            height: 50px;
            animation-delay: 3s;
            animation-duration: 19s;
        }

        .bg-particles span:nth-child(6) {
            left: 75%;
            width: 70px;
            height: 70px;
            animation-delay: 5s;
            animation-duration: 21s;
        }

        .bg-particles span:nth-child(7) {
            left: 85%;
            width: 35px;
            height: 35px;
            animation-delay: 1s;
            animation-duration: 23s;
        }

        .bg-particles span:nth-child(8) {
            left: 95%;
            width: 45px;
            height: 45px;
            animation-delay: 6s;
            animation-duration: 17s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.4;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-1100px) rotate(720deg);
                opacity: 0;
            }
        }

        /* ── Navigation bar ── */
        .navbar-custom {
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            background: rgba(10, 22, 40, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .navbar-custom .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .navbar-custom .navbar-brand span {
            color: var(--primary-light);
        }

        .navbar-custom .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-custom .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-back-home {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #fff;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-back-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.4);
            color: #fff;
        }

        /* ── Hero section ── */
        .page-hero {
            position: relative;
            z-index: 1;
            padding: 4rem 0 2rem;
            text-align: center;
        }

        .page-hero .badge-page {
            display: inline-block;
            background: rgba(13, 110, 253, 0.2);
            border: 1px solid rgba(13, 110, 253, 0.3);
            color: var(--primary-light);
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .page-hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 1rem;
        }

        .page-hero h1 .highlight {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-hero p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }

        /* ── Content Card ── */
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding-bottom: 4rem;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2.5rem 3rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }

        .content-card:hover {
            border-color: rgba(13, 110, 253, 0.3);
            box-shadow: 0 8px 40px rgba(13, 110, 253, 0.1);
        }

        .content-card .card-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.2), rgba(0, 201, 167, 0.15));
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: var(--primary-light);
        }

        .content-card .content-body {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 1rem;
        }

        .content-card .content-body h1,
        .content-card .content-body h2,
        .content-card .content-body h3,
        .content-card .content-body h4 {
            color: #fff;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }

        .content-card .content-body h1 {
            font-size: 1.75rem;
        }

        .content-card .content-body h2 {
            font-size: 1.4rem;
        }

        .content-card .content-body h3 {
            font-size: 1.2rem;
        }

        .content-card .content-body p {
            margin-bottom: 1rem;
            color: var(--text-muted);
        }

        .content-card .content-body ul,
        .content-card .content-body ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
            color: var(--text-muted);
        }

        .content-card .content-body li {
            margin-bottom: 0.4rem;
        }

        .content-card .content-body a {
            color: var(--primary-light);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .content-card .content-body a:hover {
            color: #fff;
        }

        .content-card .content-body blockquote {
            border-left: 3px solid var(--primary);
            padding-left: 1rem;
            margin: 1rem 0;
            color: var(--text-muted);
            font-style: italic;
        }

        .content-card .content-body img {
            max-width: 100%;
            border-radius: 12px;
            margin: 1rem 0;
        }

        .content-card .content-body pre {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 1rem;
            overflow-x: auto;
            color: #e2e8f0;
            font-size: 0.9rem;
        }

        /* ── Footer ── */
        .page-footer {
            position: relative;
            z-index: 1;
            padding: 2rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }

        .page-footer p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin: 0;
        }

        .page-footer a {
            color: var(--primary-light);
            text-decoration: none;
        }

        .page-footer a:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .content-card {
                padding: 1.5rem;
                border-radius: 16px;
            }

            .page-hero {
                padding: 2rem 0 1rem;
            }

            .page-hero h1 {
                font-size: 1.75rem;
            }

            .navbar-custom .navbar-brand {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .content-card {
                padding: 1.25rem;
                border-radius: 12px;
            }
        }
    </style>
</head>

<body>

    {{-- Animated Background Particles --}}
    <div class="bg-particles">
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
    </div>

    {{-- Navigation --}}
    <nav class="navbar-custom" id="mainNav">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">
                <a href="{{ url('/') }}" class="navbar-brand text-decoration-none">
                    {{ config('app.name') }}
                </a>
            </div>
    </nav>

    {{-- Hero Section --}}
    <section class="page-hero">
        <div class="container">
            <span class="badge-page">
                <i class="ri-shield-line me-1"></i> Privacy Policy
            </span>
            <h1>
                {{ $page->title ?? 'Privacy Policy' }}
            </h1>
            <p>
                Your privacy matters to us. This policy outlines how we collect, use, and protect your personal
                information.
            </p>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="content-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="content-card">
                        <div class="card-icon">
                            <i class="ri-shield-check-line"></i>
                        </div>
                        <div class="content-body">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="page-footer">
        <div class="container">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                <br class="d-sm-none">
            </p>
        </div>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>

    {{-- Navbar scroll effect --}}
    <script>
        window.addEventListener('scroll', function () {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

</body>

</html>