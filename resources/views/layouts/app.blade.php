<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Dashboard</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords"
        content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('LF1.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" id="main-font-link">

    <!-- Icon Fonts -->
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/assets/fonts/material.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('mantis/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('mantis/assets/css/style-preset.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    @include('layouts.sidebar')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('layouts.header')
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <main>
        {{ $slot }}
    </main>
    <!-- [ Main Content ] end -->
    @include('layouts.footer')

    <!-- [Page Specific JS] start -->
    <script src="{{ asset('mantis/assets/js/plugins/apexcharts.min.js') }}"></script>
    {{-- <script src="{{ asset('mantis/assets/js/pages/dashboard-default.js') }}"></script> awalnyaa ini--}}
    <script src="{{ asset('mantis/assets/js/pages/dashboard-lf.js') }}"></script>
    <!-- [Page Specific JS] end -->

    <!-- Required Js -->
    <script src="{{ asset('mantis/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('mantis/assets/js/plugins/feather.min.js') }}"></script>


    <script>
        layout_change('light');
    </script>
    <script>
        change_box_container('false');
    </script>
    <script>
        layout_rtl_change('false');
    </script>
    <script>
        preset_change("preset-1");
    </script>
    <script>
        font_change("Public-Sans");
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


    <script>
        $(document).ready(function () {
            $('#res-config').DataTable();
        });
    </script>
</body>
<!-- [Body] end -->

</html>
