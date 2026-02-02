<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartDocs - Engineering') </title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .app-sidebar {
            width: 250px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .app-sidebar h3 {
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            padding-bottom: 10px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 10px 0;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .app-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .app-header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .app-header h1 {
            color: #333;
            font-size: 24px;
        }

        .app-header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .app-main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #666;
        }

        .breadcrumb a {
            color: #2a5298;
            text-decoration: none;
            margin: 0 5px;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 5px solid;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 5px rgba(42, 82, 152, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-primary {
            background-color: #2a5298;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1e3c72;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #2a5298;
        }

        .pagination a:hover {
            background-color: #f8f9fa;
        }

        .pagination .active {
            background-color: #2a5298;
            color: white;
            border-color: #2a5298;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="app-sidebar">
            <h3>📋 SmartDocs</h3>
            <ul class="sidebar-menu">
                <li><a href="/">🏠 Dashboard</a></li>
                <li><a href="{{ route('engineering.index') }}" class="@if(Route::is('engineering.*')) active @endif">⚙️ Engineering</a></li>
                <li><a href="/">📊 Laporan</a></li>
                <li><a href="/">⚡ Operasi</a></li>
                <li><a href="/">🔧 Pemeliharaan</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="app-content">
            <!-- Header -->
            <div class="app-header">
                <h1>@yield('page_title', 'Engineering Documents')</h1>
                <div class="app-header-right">
                    <span>👤 User</span>
                </div>
            </div>

            <!-- Main Area -->
            <div class="app-main">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Mark active menu item
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
