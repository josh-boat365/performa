<x-base-layout>
    <div class="container-fluid">



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ route('kpi.index') }}">Setup For KPIs</a> > Section
                        Setup For > <a href="#">{{ session('kpi_section_name') }}</a>
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
                                            class="mdi mdi-plus me-1"></i> Create Section for KPI</button>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>

                                        <th class="align-middle">Section Name</th>
                                        <th class="align-middle">Section Score</th>
                                        <th class="align-middle">Section Description</th>
                                        {{--  <th class="align-middle">Section Metric Name</th>
                                        <th class="align-middle">Section Metric Score</th>
                                        <th class="align-middle">Section Metric Description</th>  --}}
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($filteredSections as $section)
                                        <tr>
                                            <td><a href="{{ route('metric.index', $section['id']) }}">{{ $section['name'] }}</a></td>
                                            <td>{{ $section['score'] }}</td>
                                            <td>{{ $section['description'] }}</td>
                                            {{--  <td>Velocity</td>
                                        <td>10</td>
                                        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td>  --}}
                                            <td>
                                                <a href="{{ route('show.section', $section['id']) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i>edit</span>
                                                </a>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $section['id'] }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> delete</span>
                                                </a>

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $section['id'] }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                    KPI
                                                                    Deletion</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    delete this
                                                                    KPI?</h4>
                                                                <form action="{{ route('delete.section', $section['id']) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    {{--  @method('DELETE')  --}}
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
                                                <p>No Section For KPI Created....</p>
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
                                <h5 class="modal-title" id="myLargeModalLabel">Create Section For KPI</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <form action="{{ route('create.section') }}" method="POST">
                                    @csrf
                                    <div>
                                        <div>
                                            <div class=" d-flex">
                                                <div class="w-100">
                                                    <div class="d-md-flex">
                                                        <div class="w-100">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div>
                                                                        <div class="row mb-3">
                                                                            <div class="col-xl-3 col-sm-6">
                                                                                <div class="mt-2">
                                                                                    <h5>Section Configuration</h5>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xl-9 col-sm-6">
                                                                                <div
                                                                                    class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                                                                    {{--  Section header  --}}
                                                                                    <div class="dropdown mb-0">
                                                                                        <a class="btn btn-link text-muted"
                                                                                            role="button"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true">
                                                                                            <i class="bx bxs-cog"></i>
                                                                                            Settings
                                                                                        </a>

                                                                                        <div class="dropdown-menu dropdown-menu-end"
                                                                                            style="padding: 10px !important; width:28vh;">

                                                                                            <div>
                                                                                                <input
                                                                                                    data-bs-toggle="collapse"
                                                                                                    data-bs-target="#collapseExample"
                                                                                                    aria-expanded="false"
                                                                                                    aria-controls="collapseExample"
                                                                                                    class="form-check-input"
                                                                                                    type="checkbox"
                                                                                                    id="formCheckcolor1">
                                                                                                <label
                                                                                                    class="form-check-label"
                                                                                                    for="formCheckcolor1">
                                                                                                    Enable Section
                                                                                                    Metric
                                                                                                </label>
                                                                                            </div>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <div class="mb-3 col-lg-10">
                                                                            <label for="email">Section
                                                                                Name</label>
                                                                            <input type="text" name="name"
                                                                                class="form-control"
                                                                                placeholder="Enter Section Name" />
                                                                        </div>
                                                                        <div class="mb-3 col-sm-6">
                                                                            <label for="name">Score</label>
                                                                            <input type="number" id="score"
                                                                                name="score" class="form-control"
                                                                                placeholder="Section Score" />
                                                                        </div>
                                                                        <div class="mb-3 col-lg-10">
                                                                            <label for="description">Section
                                                                                Description</label>
                                                                            <input type="text" id="description"
                                                                                name="description"
                                                                                class="form-control"
                                                                                placeholder="Enter Section Description" />
                                                                        </div>

                                                                        <input type="hidden" name="active"
                                                                            value="1">
                                                                        <input type="hidden" name="kpiId"
                                                                            value="{{ session('kpi_section_id') }}">

                                                                    </div>
                                                                </div>

                                                                <div class="collapse" id="collapseExample">
                                                                    <div class="mt-4 mb-4"
                                                                        style="background-color: gray; height: 1px;">
                                                                    </div>
                                                                    {{--  Section Metric Setup  --}}
                                                                    {{--  Section Metric Header  --}}
                                                                    <h6>Section Metric Configuration</h6>
                                                                    <div class="">
                                                                        <div class="repeater">
                                                                            <div data-repeater-list="group-b">
                                                                                <div data-repeater-item
                                                                                    class="d-flex justify-center gap-3">

                                                                                    <div class="mb-3 col-lg-8">
                                                                                        <label for="email">Metric
                                                                                            Name</label>
                                                                                        <input type="text"
                                                                                            id="description"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Description" />
                                                                                    </div>
                                                                                    <div class="mb-3 col-sm-2">
                                                                                        <label
                                                                                            for="name">Score</label>
                                                                                        <input type="number"
                                                                                            id="score"
                                                                                            name="score"
                                                                                            class="form-control"
                                                                                            placeholder="Section Score" />
                                                                                    </div>
                                                                                    <div
                                                                                        class=" col-sm-2 align-self-center">
                                                                                        <div class="p-3 mt-2">
                                                                                            <input data-repeater-delete
                                                                                                type="button"
                                                                                                class="btn btn-danger"
                                                                                                value="Delete" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="d-flex gap-3">
                                                                                <input data-repeater-create
                                                                                    type="button"
                                                                                    class="btn btn-primary"
                                                                                    value="Add" />
                                                                                <button type="submit"
                                                                                    class="btn btn-success">Save</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>


                                                        </div>
                                                    </div>
                                                    <!-- end card -->
                                                </div>
                                                <!-- end w-100 -->
                                            </div>

                                        </div>

                                        <div class="card ms-lg-2" style="height: fit-content">
                                            <div class="card-body">

                                                <div class="d-grid mb-2">
                                                    <button type="submit" class="btn btn-success">Create
                                                        section</button>
                                                </div>

                                            </div>
                                        </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>





    </div>

</x-base-layout>
