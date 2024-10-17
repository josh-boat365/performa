<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Overview of Batches of KPIs</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <table class="table mb-0">

            <thead class="table-light">
                <tr>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <div class="col-md-12">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                  <img style="width: 8%" src="{{ asset('bpsl_imgs/performa-short-3.png') }}" alt="">
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('kpi.form') }}">
                                        <h3>January 2025 Batch - First Quarter</h3>
                                    </a>
                                </div>
                        </div>
                    </div>
                </tr>
                <tr>
                    <div class="col-md-12">
                        <div class="card card-body">
                            <div class="d-flex">
                                <div>
                                  <img style="width: 8%" src="{{ asset('bpsl_imgs/performa-short-3.png') }}" alt="">
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#">
                                        <h3>January 2025 Batch - Second Quarter</h3>
                                    </a>
                                </div>
                        </div>
                    </div>
                </tr>

            </tbody>
        </table>

        {{--  Paginator  --}}
        <nav aria-label="Page navigation example" class="mt-2">
            <ul class="pagination justify-content-end">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>

    </div>




</x-base-layout>
