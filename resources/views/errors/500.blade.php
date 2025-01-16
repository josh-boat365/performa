<!doctype html>
<html lang="en">

@include('layouts.head')

<body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">


        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->



        <div class="container-fluid px-1">
            <div class="account-pages my-5 pt-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mb-5">
                                <h1 class="display-2 fw-medium">5<i
                                        class="bx bx-buoy bx-spin text-primary display-3"></i>0</h1>
                                <h4 class="text-uppercase">Internal Server Error</h4>
                                <div class="mt-5 text-center">
                                    <a class="btn btn-primary waves-effect waves-light"
                                        href="{{ route('dashboard.index') }}">Back to
                                        Dashboard</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-4 col-xl-4">
                            <div>
                                <img src="{{ asset('assets/images/error-img.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>

                    <div class="text-center">

                        <div>
                            <img style="margin: 2rem auto 0 auto; width: 10rem; padding: 0.5rem"
                                src="{{ asset('bpsl_imgs/purple-logo-bpsl.png') }}" alt="">
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Powered By BPSL | IT Department - Application
                                Support
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

</body>


</html>
