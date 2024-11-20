<x-base-layout>
    <div class="container-fluid">



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ route('kpi.index') }}">Setup For Sections</a> > Metric
                        Setup For > <a href="#">{{ session('section_metric_name') }}</a>
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>



        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-8">
                                <div class="search-box me-2 mb-2 d-inline-block" @style(['width: 50%'])>
                                    <div class="position-relative">
                                        <input type="text" class="form-control" autocomplete="off"
                                            id="searchTableList" placeholder="Search...">
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-sm-end">
                                    <button type="button" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl"
                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2 "><i
                                            class="mdi mdi-plus me-1"></i> Create Metric for Section</button>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>

                                        <th class="align-middle">Metric Name</th>
                                        <th class="align-middle">Metric Score</th>
                                        <th class="align-middle">Metric Description</th>
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($filteredMetrics as $metric)
                                        <tr>
                                            <td><a href="#">{{ $metric['name'] }}</a></td>
                                            <td>{{ $metric['score'] }}</td>
                                            <td>{{ $metric['description'] }}</td>

                                            <td>
                                                <a href="{{ route('show.metric', $metric['id']) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i>edit</span>
                                                </a>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $metric['id'] }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> delete</span>
                                                </a>

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $metric['id'] }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                    Metric
                                                                    Deletion</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    delete this
                                                                    Metric?</h4>
                                                                <form
                                                                    action="{{ route('delete.metric', $metric['id']) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <div class="d-grid">
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Yes,
                                                                            Delete</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <p>No Metric For Section Created....</p>
                                            </td>
                                        </tr>
                                    @endforelse


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!--  Large modal example -->
                <div class="modal fade bs-example-modal-xl" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl ">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">Create Metric For Section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <form action="{{ route('create.metric') }}" method="POST">
                                    @csrf

                                    <div class="mb-3 col-lg-10">
                                        <label for="email">Metric Name
                                        </label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter Metric Name" />
                                    </div>
                                    <div class="mb-3 col-sm-6">
                                        <label for="name">Score</label>
                                        <input type="number" id="score" name="score" class="form-control"
                                            placeholder="Metric Score" />
                                    </div>
                                    <div class="mb-3 col-lg-10">
                                        <label for="description">Metric
                                            Description</label>
                                        <input type="text" id="description" name="description"
                                            class="form-control" placeholder="Enter Metric Description" />
                                    </div>

                                    <input type="hidden" name="active" value="1">
                                    <input type="hidden" name="sectionId" value="{{ (session('section_metric_id')) }}">


                                    <div class="d-grid mb-2">
                                        <button type="submit" class="btn btn-success">Create
                                            Metric</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end w-100 -->
    </div>





</x-base-layout>
