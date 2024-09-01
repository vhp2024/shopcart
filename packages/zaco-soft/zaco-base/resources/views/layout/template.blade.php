
<!DOCTYPE html>
<html class="loading semi-dark-layout" lang="en" data-layout="semi-dark-layout" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Dashboard ecommerce - Vuexy - Bootstrap HTML admin template</title>
    <link rel="apple-touch-icon" href="/themes/admin/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/themes/admin/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/themes/admin/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/vendors/css/extensions/toastr.min.css">
    @stack('style')
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/components.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/pages/dashboard-ecommerce.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/themes/admin/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  menu-expanded" data-open="click" data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('zaco-base::layout.includes.header')
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    @include('zaco-base::layout.includes.main-menu')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper container-xxl p-0">
            @yield('content')
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021<a class="ms-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-end d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>

    @if(session('error_msg'))
        <div class="toast basic-toast position-fixed top-0 end-0 m-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="../../../app-assets/images/logo/logo.png" class="me-1" alt="Toast image" height="18" width="25">
                <strong class="me-auto">Vue Admin</strong>
                <small class="text-muted">11 mins ago</small>
                <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">{{session('error_msg')}}</div>
        </div>
    @endif
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="/themes/admin/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <!-- <script src="/themes/admin/vendors/js/charts/apexcharts.min.js"></script> -->
    <script src="/themes/admin/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/themes/admin/js/core/app-menu.js"></script>
    <script src="/themes/admin/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <!-- <script src="/themes/admin/js/scripts/pages/dashboard-ecommerce.js"></script> -->
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

            try {
                var basicToast = document.querySelector('.basic-toast');
                if(basicToast)
                {
                    var showBasicToast = new bootstrap.Toast(basicToast);
                    showBasicToast.show();
                    console.log('2022-06-26 09:57:41---SHOW TOAST');
                }
            } catch (error) {}
        })
    </script>
    @stack('script')
</body>
<!-- END: Body-->

</html>
