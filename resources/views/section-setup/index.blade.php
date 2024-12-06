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

        <div>
            <a href="{{ route('create.section') }}" class="btn btn-success btn-rounded waves-effect waves-light "><i
                    class="bx bxs-plus"></i>Create
                Section</a>
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
                                <!-- Additional controls can go here -->
                                <p class="font-bold">Total Score </p>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>
                                        <th class="align-middle">Section’s KPI Name</th>
                                        <th class="align-middle">Section Name</th>
                                        <th class="align-middle">Section Score</th>
                                        <th class="align-middle">Section Description</th>
                                        <th class="align-middle">Section state</th>
                                        {{--  <th class="align-middle">Created At</th>  --}}
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sections as $section)
                                        <tr>
                                            <td><a href="#">{{ $section->kpi->name }}</a></td>
                                            <td><a href="#" @style(['text-wrap: auto'])>{{ $section->name }}</a></td>
                                            <td>{{ $section->score }}</td>

                                            <td><span @style(['text-wrap: auto'])>{{ $section->description }}</span></td>
                                            <td>
                                                <span
                                                    class="dropdown badge rounded-pill {{ $section->active ? 'bg-success' : 'bg-dark' }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ $section->active ? 'Activated' : 'Deactivated' }}
                                                </span>
                                            </td>

                                            {{--  <td>{{ Carbon\Carbon::parse($section->createdAt)->diffForHumans() }}
                                            </td>  --}}
                                            <td>
                                                <a href="{{ route('show.section', $section->id) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i> Edit</span>
                                                </a>
                                                {{--  <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $section->id }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> Delete</span>
                                                </a>  --}}

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $section->id }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                    Section Deletion</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    delete this Section?</h4>
                                                                <p>Deleting a <b>Section</b> means removing it from the
                                                                    <b>system entirely</b> and you cannot <b>recover</b>
                                                                    it again</p>
                                                                <form
                                                                    action="{{ route('delete.section', $section->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="d-grid">
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Yes, Delete</button>
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
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('searchTableList').addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('#order-list tbody tr');

                    tableRows.forEach(row => {
                        const sectionKPI = row.querySelector('td:nth-child(1) a').textContent.toLowerCase();
                        const sectionName = row.querySelector(' td:nth-child(2) a').textContent.toLowerCase();
                        const sectionScore = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const sectionDescription = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                        const createdAt = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                        // Check if any of the fields contain the search term
                        if (sectionKPI.includes(searchTerm) || sectionName.includes(searchTerm) || sectionScore
                            .includes(searchTerm) ||
                            sectionDescription.includes(searchTerm) || createdAt.includes(searchTerm)) {
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
