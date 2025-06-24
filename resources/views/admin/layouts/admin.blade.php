<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            transition: all 0.3s;
        }
        .sidebar.active {
            margin-left: -250px;
        }
        .content {
            transition: all 0.3s;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .border-left-primary {
            border-left: 4px solid #4e73df;
        }
        .border-left-success {
            border-left: 4px solid #1cc88a;
        }
        .border-left-info {
            border-left: 4px solid #36b9cc;
        }
        .border-left-warning {
            border-left: 4px solid #f6c23e;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        @auth('admin')
            @include('admin.layouts.sidebar')
        @endauth
        
        <div class="content w-100">
            @include('admin.layouts.navbar')
            
            <main class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('.sidebar').toggleClass('active');
            });

            // Auto close alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>