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


        <div>
            <a href="{{ route('create.kpi') }}" class="btn btn-success btn-rounded waves-effect waves-light "><i
                    class="bx bxs-plus"></i>Create
                KPI</a>
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
                    <!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                        id="order-list">
                        <thead class="table-light">
                            <tr>

                                <th class="align-middle">KPI Name</th>
                                <th class="align-middle">Type</th>
                                <th class="align-middle">Role</th>
                                {{--  <th class="align-middle">Department</th>  --}}
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
                                    {{--  <td>
                                        <span class="badge rounded-pill bg-primary">{{ $kpi->empRole->department }}</span>
                                    </td>  --}}
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
                                                                <p>Deleting a <b>KPI</b> means removing it from the <b>system entirely</b> and you cannot <b>recover</b> it again</p>
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

        @push('scripts')
            <script>
                document.getElementById('searchTableList').addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('#order-list tbody tr');

                    tableRows.forEach(row => {
                        const kpiName = row.querySelector('th a').textContent.toLowerCase();
                        const type = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                        const role = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const department = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                        const batch = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                        const supervisors = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                        const active = row.querySelector('td:nth-child(7)').textContent.toLowerCase();
                        const createdAt = row.querySelector('td:nth-child(8)').textContent.toLowerCase();

                        // Check if any of the fields contain the search term
                        if (kpiName.includes(searchTerm) || type.includes(searchTerm) || role.includes(
                            searchTerm) ||
                            department.includes(searchTerm) || batch.includes(searchTerm) ||
                            supervisors.includes(searchTerm) || active.includes(searchTerm) ||
                            createdAt.includes(searchTerm)) {
                            row.style.display = ''; // Show the row
                        } else {
                            row.style.display = 'none'; // Hide the row
                        }
                    });
                });
            </script>
        @endpush


    </div>

</x-base-layout>
