<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Create Global Kpis For Roles</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="d-flex gap-3">
            <a href="{{ route('create.global.kpi') }}" class="btn btn-success btn-rounded waves-effect waves-light "><i
                    class="bx bxs-plus"></i>Create Global
            </a>


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
                                <th class="align-middle">Description</th>
                                <th class="align-middle">Type</th>
                                <th class="align-middle">Batch</th>
                                <th class="align-middle">State</th>
                                <th class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeKpis as $kpi)
                                <tr>
                                    <th scope="row">
                                        <a href="#">{{ $kpi['name'] }}</a>
                                    </th>
                                    <td>{{ $kpi['description'] }}</td>
                                    <td>
                                        <span style="cursor: pointer;" class="dropdown badge rounded-pill bg-primary"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $kpi['type'] }}
                                            <div class="dropdown-menu">
                                                @if ($kpi['type'] == 'PROBATION')
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#status-modal-{{ $kpi['id'] }}">GLOBAL</a>
                                                @elseif($kpi['type'] == 'GLOBAL')
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#status-modal-{{ $kpi['id'] }}">PROBATION</a>
                                                @endif
                                            </div>
                                        </span>

                                        <!-- Modal for Confirmation -->
                                        <div class="modal fade" id="status-modal-{{ $kpi['id'] }}" tabindex="-1"
                                            role="dialog" aria-labelledby="statusModalLabel-{{ $kpi['id'] }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="statusModalLabel-{{ $kpi['id'] }}">
                                                            Confirm KPI Status Update
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="text-center mb-4">
                                                            Are you sure you want to change the status to
                                                            <b>{{ $kpi['type'] == 'PROBATION' ? 'GLOBAL' : 'PROBATION' }}</b>?
                                                        </h4>
                                                        <form
                                                            action="{{ route('update.global.kpi.status', $kpi['id']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status"
                                                                value="{{ $kpi['type'] == 'PROBATION' ? 'GLOBAL' : 'PROBATION' }}">
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
                                    <td>{{ $kpi['batch']['name'] }}</td>
                                    <td>
                                        <span
                                            class="dropdown badge rounded-pill {{ $kpi['active'] ? 'bg-success' : 'bg-dark' }}"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $kpi['active'] ? 'Activated' : 'Deactivated' }}
                                            <div class="dropdown-menu">
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#kpiModal-{{ $kpi['id'] }}">
                                                    {{ $kpi['active'] ? 'Deactivate' : 'Activate' }}
                                                </a>
                                            </div>
                                        </span>
                                        <!-- Modal for KPI Activation/Deactivation -->
                                        <div class="modal fade" id="kpiModal-{{ $kpi['id'] }}" tabindex="-1"
                                            role="dialog" aria-labelledby="kpiModalLabel-{{ $kpi['id'] }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="kpiModalLabel-{{ $kpi['id'] }}">
                                                            Confirm KPI State Update</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></ button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="text-center mb-4">Are you sure you want to
                                                            {{ $kpi['active'] ? 'Deactivate' : 'Activate' }}?</h4>
                                                        <form action="{{ route('update.global.kpi.state', $kpi['id']) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="active"
                                                                value="{{ $kpi['active'] ? 0 : 1 }}">
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
                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('show.global.kpi', $kpi['id']) }}">
                                                <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                        class="bx bxs-pencil"></i> Edit</span>
                                            </a>
                                            <!-- Modal for Delete Confirmation -->
                                            <div class="modal fade bs-delete-modal-lg-{{ $kpi['id'] }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="deleteModalLabel-{{ $kpi['id'] }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-md modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteModalLabel-{{ $kpi['id'] }}">Confirm
                                                                Global KPI Deletion</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4 class="text-center mb-4">Are you sure you want to
                                                                delete this Global KPI?</h4>
                                                            <form action="{{ route('delete.global.kpi', $kpi['id']) }}"
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
