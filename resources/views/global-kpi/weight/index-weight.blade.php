<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Create Global Weighted Score For Departments</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="d-flex gap-3">
            <a href="{{ route('create.global.weight') }}" class="btn btn-success btn-rounded waves-effect waves-light "><i
                    class="bx bxs-plus"></i>Create Weight
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

                                <th class="align-middle">Global KPI Name</th>
                                <th class="align-middle">Department</th>
                                <th class="align-middle">Type</th>
                                <th class="align-middle">Weighted Score For Department</th>
                                {{--  <th class="align-middle">Created At</th>  --}}
                                <th class="align-middle">Action</th>

                        </thead>
                        <tbody>
                            @forelse ($activeKpis as $kpi)
                                <tr>
                                    <th scope="row">
                                        <a href="#">{{ $kpi->kpi->name }}</a>
                                    </th>
                                    <td>
                                        {{ $kpi->department->name }}
                                    </td>
                                    <td>
                                        <span @style(['cursor: pointer']) class="dropdown badge rounded-pill bg-primary"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $kpi->kpi->type }}
                                        </span>

                                    </td>

                                    </td>
                                    <td>{{ $kpi->weight }}</td>

                                    {{--  <td>{{ Carbon\Carbon::parse($kpi->createdAt)->diffForHumans() }}</td>  --}}
                                    <td>
                                        <div class="d-flex gap-3">
                                            <a href="{{ route('show.global.weight', $kpi->id) }}">
                                                <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                        class="bx bxs-pencil"></i>edit</span>
                                            </a>
                                            {{--  <a href="#" data-bs-toggle="modal"
                                                data-bs-target=".bs-delete-modal-lg-{{ $kpi->id }}">
                                                <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                        class="bx bxs-trash"></i> delete</span>
                                            </a>  --}}

                                            <!-- Modal for Delete Confirmation -->
                                            <div class="modal fade bs-delete-modal-lg-{{ $kpi->id }}"
                                                tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                Global Weight Department Score Delete</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4 class="text-center mb-4">Are you sure you want to
                                                                delete this Global <b>Weight Score</b> for
                                                                <b>{{ $kpi->department->name }} </b> Department?
                                                            </h4>
                                                            <form
                                                                action="{{ route('delete.global.weight', $kpi->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                {{--  @method('DELETE')  --}}
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
