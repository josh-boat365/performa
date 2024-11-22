<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Create KPI for Department Role</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div style="">

            <button type="button" class="btn btn-success btn-rounded waves-effect waves-light " data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="bx bxs-plus"></i>Create
                KPI</button>
        </div>
        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>


        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <div class="search-box me-2 mb-2 d-inline-block" @style(['width: 50%'])>
                            <div class="position-relative">
                                <input type="text" class="form-control" autocomplete="off" id="searchTableList"
                                    placeholder="Search...">
                                <i class="bx bx-search-alt search-icon"></i>
                            </div>
                        </div>
                    </div>
                    {{--  <div class="col-sm-4">
                        <div class="text-sm-end">
                            <button type="button" class="btn btn-success btn-rounded waves-effect waves-light "
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                                aria-controls="offcanvasRight"><i class="bx bxs-plus"></i> + Create KPI</button>
                        </div>
                    </div>  --}}
                    <!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                        id="order-list">
                        <thead class="table-light">
                            <tr>

                                <th class="align-middle">KPI Name</th>
                                {{--  <th class="align-middle">Score</th>  --}}
                                <th class="align-middle">Type</th>
                                <th class="align-middle">Role</th>
                                <th class="align-middle">Department</th>
                                <th class="align-middle">Batch</th>
                                <th class="align-middle">Supervisors</th>
                                <th class="align-middle">Active</th>
                                <th class="align-middle">Created At</th>
                                <th class="align-middle">Action</th>

                        </thead>
                        <tbody>
                            @forelse ($activeKpis as $kpi)
                                <tr>
                                    <th scope="row">
                                        <a href="#">{{ $kpi->name }}</a>
                                    </th>

                                    <td>
                                        @if ($kpi->type == 'REGULAR')
                                            <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-primary"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ $kpi->type }}
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="updateModalText('PROBATION')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">PROBATION</a>

                                                    <a class="dropdown-item" onclick="updateModalText('GLOBAL')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">GLOBAL</a>
                                                </div>
                                            </span>
                                        @elseif($kpi->type == 'PROBATION')
                                            <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-primary"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ $kpi->type }}
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="updateModalText('REGULAR')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">REGULAR</a>
                                                    <a class="dropdown-item" onclick="updateModalText('GLOBAL')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">GLOBAL</a>
                                                </div>
                                            </span>
                                        @elseif($kpi->type == 'GLOBAL')
                                            <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-primary"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ $kpi->type }}
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="updateModalText('REGULAR')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">REGULAR</a>
                                                    <a class="dropdown-item" onclick="updateModalText('PROBATION')"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target=".bs-status-modal-xl">PROBATION</a>
                                                </div>
                                            </span>
                                        @endif

                                        <!-- Modal for Confirmation -->
                                        <div class="modal fade bs-status-modal-xl" tabindex="-1" role="dialog"
                                            aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Confirm KPI
                                                            Status
                                                            Update</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 id="modal-status-text" class="text-center mb-4">
                                                        </h4>
                                                        <form action="{{ route('update.kpi.status', $kpi->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status"
                                                                value="{{ $kpi->type == 'REGULAR' ? 'PROBATION' : 'GLOBAL' }}">
                                                            <div class="d-grid">
                                                                <button type="submit"
                                                                    class="btn btn-success">Yes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @push('scripts')
                                            <script>
                                                function updateModalText(status) {
                                                    const modalText = document.getElementById('modal-status-text');
                                                    modalText.innerHTML = `Are you sure you want to change the status to '${status}'?`;
                                                }
                                            </script>
                                        @endpush
                                    </td>
                                    <td>{{ $kpi->empRole->name }}</td>
                                    <td><span class="badge rounded-pill bg-primary">{{ $kpi->department->name }}</span>
                                    </td>
                                    <td>{{ $kpi->batch->name }}</td>
                                    <td>
                                        <span @style(['cursor: pointer']) class=" badge rounded-pill bg-success">
                                            {{--  {{ $kpi['department']['manager'] }}  --}}
                                            Department Head
                                        </span>
                                        <span @style(['cursor: pointer']) class=" badge rounded-pill bg-primary">
                                            {{--  {{ $kpi['empRole']['manager'] }}  --}}
                                            Department Manager
                                        </span>
                                    </td>
                                    <td>
                                        <span @style(['cursor: pointer'])
                                            class="dropdown badge rounded-pill {{ $kpi->active ? 'bg-success' : 'bg-dark' }}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $kpi->active ? 'Activated' : 'Deactivated' }}
                                            <div class="dropdown-menu">
                                                <a href="" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target=".bs-example-modal-lg" class="m-2">
                                                    {{ $kpi->active ? 'Deactivate' : 'Activate' }}</a>
                                            </div>
                                        </span>
                                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                                            aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-centered ">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myLargeModalLabel">Confirm KPI
                                                            State
                                                            Update</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="text-center mb-4"> Are you sure, you want to
                                                            {{ $kpi->active ? 'Deactivate' : 'Activate' }} ?</h4>
                                                        <form action="{{ route('update.kpi.state', $kpi->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="active"
                                                                value="{{ $kpi->active ? 0 : 1 }}">
                                                            <div class="d-grid">
                                                                <button type="submit"
                                                                    class="btn btn-success">Yes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($kpi->createdAt)->format('jS F, Y : g:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('show.kpi', $kpi->id) }}">
                                                <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                        class="bx bxs-pencil"></i>edit</span>
                                            </a>
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target=".bs-delete-modal-lg-{{ $kpi->id }}">
                                                <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                        class="bx bxs-trash"></i> delete</span>
                                            </a>

                                            <!-- Modal for Delete Confirmation -->
                                            <div class="modal fade bs-delete-modal-lg-{{ $kpi->id }}"
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
                                                            <form action="{{ route('delete.kpi', $kpi->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <div class="d-grid">
                                                                    <button type="submit" class="btn btn-danger">Yes,
                                                                        Delete</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6">
                                        <p>No KPI For Role Created....</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation example" class="mt-3">
                    {{ $activeKpis->links() }}
                </nav>
            </div>
        </div>


        <!-- right offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
            aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Setup For KPI</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('create.kpi') }}" class="custom-validation" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Name</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="name" required
                                placeholder="Enter Name for KPI" value="{{ old('name') }}"
                                id="example-text-input">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Description</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="description" required
                                placeholder="Enter Description for KPI" value="{{ old('description') }}"
                                id="example-text-input">
                        </div>
                    </div>

                    {{--  <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Score</label>
                        <div class="col-md-12">
                            <input class="form-control" type="number" name="score" required
                                value="{{ old('score') }}" id="example-text-input">
                        </div>
                    </div>  --}}

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select KPI Type</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Select KPI type</option>
                                <option value="REGULAR" {{ old('type') == 'REGULAR' ? 'selected' : '' }}> REGULAR
                                </option>
                                <option value="PROBATION" {{ old('type') == 'PROBATION' ? 'selected' : '' }}>
                                    PROBATION </option>
                                <option value="GLOBAL" {{ old('type') == 'GLOBAL' ? 'selected' : '' }}> GLOBAL
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Department</label>
                        <div class="col-md-12">
                            <select name="departmentId" class="form-select">
                                <option>Select Department</option>

                                @foreach ($uniqueDepartments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('departmentId') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Role</label>
                        <div class="col-md-12">
                            <select name="empRoleId" class="form-select">
                                <option>Select Role</option>

                                @foreach ($uniqueRoles as $role)
                                    <option value="{{ $role['id'] }}"
                                        {{ old('empRoleId') == $role['id'] ? 'selected' : '' }}> {{ $role['name'] }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Batch</label>
                        <div class="col-md-12">
                            <select name="batchId" class="form-select">
                                <option>Select Batch</option>
                                @foreach ($batch_data as $batch)
                                    <option value="{{ $batch->id }}"
                                        {{ old('batchId') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="active" value="1">
                    <input type="hidden" name="score" value="100">

                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                        Create
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-base-layout>
