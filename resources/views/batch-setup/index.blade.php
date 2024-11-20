<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Appraisal Batch Setup For KPIs</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div style="">

            <button type="button" class="btn btn-success btn-rounded waves-effect waves-light " data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                    class="bx bxs-plus"></i>Create</button>
        </div>
        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        {{--  <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Special title treatment</h3>
                    <p class="card-text">With supporting text below as a natural lead-in to additional
                        content.</p>
                    <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light">Go somewhere</a>
                </div>
            </div>
        </div>  --}}

        <div class="table-responsive">
            <table class="table table-borderless table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Batch Name</th>
                        <th>Batch Short Name</th>
                        <th>Created at</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    {{--  {{ dd($batches) }}  --}}

                    @forelse ($batches as $batch)
                        <tr>
                            <th scope="row"><a href="#">{{ $batch['name'] }}</a></th>
                            <td>{{ $batch['shortName'] }}</td>
                            <td>{{ Carbon\Carbon::parse($batch['createdAt'])->format('jS F, Y : g:i A') }}</td>
                            <td>
                                <span @style(['cursor: pointer'])
                                    class="dropdown badge rounded-pill {{ $batch['active'] ? 'bg-success' : 'bg-dark' }}"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $batch['active'] ? 'Activate' : 'Deactivated' }}
                                    <div class="dropdown-menu">
                                        <a href="" class="dropdown-item" data-bs-toggle="modal" data-bs-target=".bs-example-modal-lg"
                                            class="m-2"> {{ $batch['active'] ? 'Deactivate' : 'Activate' }}</a>
                                    </div>
                                </span>
                                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered ">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm Batch State
                                                    Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4"> Are you sure, you want to
                                                    {{ $batch['active'] ? 'Deactivate' : 'Activate' }} ?</h4>
                                                <form action="{{ route('update.batch.state', $batch['id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="active"
                                                        value="{{ $batch['active'] ? 0 : 1 }}">
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-success">Yes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($batch['status'] == 'PENDING')
                                    <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-warning"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Pending
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target=".bs-status-modal-lg">Open</a>
                                        </div>
                                    </span>
                                @elseif($batch['status'] == 'OPEN')
                                    <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-primary"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Open
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target=".bs-status-modal-lg">Close</a>
                                        </div>
                                    </span>
                                @elseif($batch['status'] == 'CLOSED')
                                    <span class="badge rounded-pill bg-dark">Closed</span>
                                @endif

                                <!-- Modal for Confirmation -->
                                <div class="modal fade bs-status-modal-lg" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm Batch Status
                                                    Update</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to change the status
                                                    to {{ $batch['status'] == 'PENDING' ? 'Open' : 'Close' }}?</h4>
                                                <form action="{{ route('update.batch.status', $batch['id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status"
                                                        value="{{ $batch['status'] == 'PENDING' ? 'OPEN' : 'CLOSED' }}">
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-success">Yes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex gap-3">
                                    <a href="{{ route('show.batch', $batch['id']) }}">
                                        <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                class="bx bxs-pencil"></i>edit</span>
                                    </a>
                                    <a href="#" data-bs-toggle="modal"
                                        data-bs-target=".bs-delete-modal-lg-{{ $batch['id'] }}">
                                        <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                class="bx bxs-trash"></i> delete</span>
                                    </a>

                                    <!-- Modal for Delete Confirmation -->
                                    <div class="modal fade bs-delete-modal-lg-{{ $batch['id'] }}" tabindex="-1"
                                        role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="myLargeModalLabel">Confirm Batch
                                                        Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4 class="text-center mb-4">Are you sure you want to delete this
                                                        batch?</h4>
                                                    <form action="{{ route('delete.batch', $batch['id']) }}"
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
                                <p>No Batch Created....</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <nav aria-label="Page navigation example" class="mt-3">
                {{ $batches->links() }}
            </nav>
        </div>



        <!-- right offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
            aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Batch Setup For Appraisal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('create.batch') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Batch Name</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="name" required
                                value="{{ old('name') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="example-text-input" class="">Batch Year</label>
                        <div class="col-md-12">
                            <select name="year" id="yearSelect" class="form-select">
                                <option>Select Year</option>
                                @for ($year = 2024; $year <= 2032; $year++)
                                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    {{--  <input type="hidden" name="shortName" value="A1H2025">  --}}
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12  mt-4">
                        Create
                    </button>
                </form>

            </div>
        </div>

    </div>




</x-base-layout>
