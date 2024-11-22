<x-base-layout>
    <div class="container-fluid">



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="#">Setup For KPIs</a> > Section
                        Setup
                        {{--  > <a href="#">{{ session('kpi_section_name') }}</a>  --}}
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div style="">

            <button type="button" class="btn btn-success btn-rounded waves-effect waves-light " data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="bx bxs-plus"></i>Create
                Section for KPI</button>
        </div>

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
                                {{--  <div class="text-sm-end">
                                    <button type="button" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl"
                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2 "><i
                                            class="mdi mdi-plus me-1"></i> Create Section for KPI</button>
                                </div>  --}}
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-wrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>

                                        <th class="align-middle">Section KPI</th>
                                        <th class="align-middle col-10">Section Name</th>
                                        <th class="align-middle">Section Score</th>
                                        <th class="align-middle">Section Description</th>
                                        <th class="align-middle">Created At</th>
                                        {{--  <th class="align-middle">Section Metric Name</th>
                                        <th class="align-middle">Section Metric Score</th>
                                        <th class="align-middle">Section Metric Description</th>  --}}
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sections as $section)
                                        <tr>
                                            <td><a href="#">{{ $section->kpi->name }}</a></td>
                                            <td><a href="#">{{ $section->name }}</a></td>
                                            <td>{{ $section->score }}</td>
                                            <td>{{ $section->description }}</td>
                                            <td>{{ Carbon\Carbon::parse($section->createdAt)->format('jS F, Y : g:i A') }}
                                            </td>

                                            {{--  <td>Velocity</td>
                                        <td>10</td>
                                        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td>  --}}
                                            <td>
                                                <a href="{{ route('show.section', $section->id) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i>edit</span>
                                                </a>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $section->id }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> delete</span>
                                                </a>

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $section->id }}"
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
                                                                <form
                                                                    action="{{ route('delete.section', $section->id) }}"
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
                        <nav aria-label="Page navigation example" class="mt-3">
                            {{ $sections->links() }}
                        </nav>
                    </div>
                </div>


                <!-- right offcanvas -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                    aria-labelledby="offcanvasRightLabel">
                    <div class="offcanvas-header">
                        <h5 id="offcanvasRightLabel">Setup For Section</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form action="{{ route('create.section') }}" class="custom-validation" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <label for="example-text-input" class="">Section Name</label>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" name="name" required
                                        placeholder="Enter Name for Section" value="{{ old('name') }}"
                                        id="example-text-input">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="example-text-input" class="">Section Description</label>
                                <div class="col-md-12">
                                    <input class="form-control" type="text" name="description" required
                                        placeholder="Enter Description for KPI" value="{{ old('description') }}"
                                        id="example-text-input">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="example-text-input" class="">Section Score</label>
                                <div class="col-md-12">
                                    <input class="form-control" type="number" name="score" required
                                        value="{{ old('score') }}" id="example-text-input">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="example-text-input" class="">Select KPI</label>
                                <div class="col-md-12">
                                    <select name="kpiId" class="form-select">
                                        <option>Select KPI</option>

                                        @foreach ($activeKpis as $kpi)
                                            <option value="{{ $kpi->id }}"
                                                {{ old('kpiId') == $kpi->id ? 'selected' : '' }}>
                                                {{ $kpi->name }} - {{ $kpi->department->name }} -
                                                {{ $kpi->empRole->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="active" value="1">

                            <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                                Create
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>





    </div>

</x-base-layout>
