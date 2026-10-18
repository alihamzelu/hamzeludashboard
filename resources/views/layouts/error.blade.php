<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'خطا') | ProSync</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .error-card {
            width: 100%;
            max-width: 520px;
            text-align: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 48px 32px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.05);
        }

        .error-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
            color: #0f172a;
            font-size: 20px;
            font-weight: 800;
            text-decoration: none;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #2563eb;
            color: white;
            font-size: 20px;
        }

        .error-code {
            margin: 0;
            color: #2563eb;
            font-size: clamp(64px, 14vw, 104px);
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -4px;
        }

        .error-title {
            margin: 18px 0 12px;
            font-size: 24px;
            font-weight: 700;
        }

        .error-description {
            margin: 0 auto;
            max-width: 380px;
            color: #64748b;
            font-size: 14px;
            line-height: 2.1;
        }

        .error-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .error-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 20px;
            border-radius: 12px;
            background: #2563eb;
            color: white;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #2563eb;
            transition: background .2s, transform .2s;
        }

        .error-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .error-button-secondary {
            background: white;
            color: #334155;
            border-color: #e2e8f0;
        }

        .error-button-secondary:hover {
            background: #f8fafc;
        }

        .error-footer {
            margin-top: 36px;
            color: #94a3b8;
            font-size: 11px;
        }

        @media (max-width: 480px) {
            .error-card {
                padding: 36px 20px;
                border-radius: 20px;
            }

            .error-title {
                font-size: 21px;
            }

            .error-actions {
                flex-direction: column;
            }

            .error-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="error-page">
        <section class="error-card">
            <a href="{{ url('/') }}" class="error-brand">
                <span class="brand-icon">P</span>
                <span>ProSync</span>
            </a>

            @yield('content')

            <div class="error-footer">
                © {{ date('Y') }} ProSync — تمام حقوق محفوظ است.
            </div>
        </section>
    </main>
</body>
</html>