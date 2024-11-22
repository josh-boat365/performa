<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Available KPIs For You</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="col-md-12">
            {{--  <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('kpi.form') }}">
                        <h4>January 2025 Batch - First Quarter</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">16</span>
                    </div>



                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 0%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </div>

            </div>  --}}
            @forelse ($activeBatches as $batch)

            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('kpi.form', $batch['id']) }}">
                        <h4>{{ $batch['name'] }}</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">{{ $batch['count'] }}</span>
                    </div>

                    <div>
                        <a href="#">
                            <span class="badge rounded-pill bg-primary fonte-size-13"><i class="bx bxs-pencil"></i>edit</span>
                        </a>
                        <a href="#">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>View</span>
                        </a>

                    </div>
                    <div>
                        {{--  <span class="badge rounded-pill bg-warning fonte-size-13">{{ $batch['status'] == 'true' ? 'In-Progress' : 'Submitted' }}</span>  --}}

                    </div>

                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 0%; font-weight: 900;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </div>

            </div>
            @empty
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <h3>No Kpis Available For You</h3>

                </div>


            </div>
            @endforelse

            {{--  <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('kpi.form') }}">
                        <h4>January 2025 Batch - First Quarter</h4>
                    </a>
                    <div>
                        <span class="badge rounded-pill bg-dark fonte-size-13">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary fonte-size-13">16</span>
                    </div>
                     <div>

                        <a href="#">
                            <span class="badge rounded-pill bg-dark fonte-size-13"><i class="bx bx-show-alt"></i>View</span>
                        </a>

                    </div>
                    <div>
                        <span class="badge rounded-pill bg-success fonte-size-13">Submitted</span>
                    </div>

                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 25%; font-weight: 900;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">70%</div>
                    </div>
                </div>

            </div>  --}}
        </div>



    </div>




</x-base-layout>
