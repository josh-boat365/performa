<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Active Batch</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="col-md-12">

            @if (!empty($activeBatches))
                <div class="card card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('show.batch.kpi', $activeBatches['id']) }}">
                            <h4>{{ $activeBatches['batch_name'] }}</h4>
                        </a>
                        {{--  <div>
                            <span class="badge rounded-pill bg-dark fonte-size-13">Global Kpi</span>
                            <span
                                class="badge rounded-pill bg-primary fonte-size-13">Personal Kpi</span>
                            <!-- Display section count -->
                        </div>  --}}
                        <div>
                            <a href="{{ route('show.batch.kpi', $activeBatches['id']) }}">
                                <span class="badge rounded-pill bg-dark fonte-size-13"><i
                                        class="bx bx-show-alt"></i>Open Batch</span>
                            </a>
                        </div>
                    </div>

                </div>
            @else
                <div class="card card-body">
                    <div class="d-flex justify-content-between">
                        <h3>No Active Appraisals Available </h3>
                    </div>
                </div>
            @endif

        </div>



    </div>




</x-base-layout>
