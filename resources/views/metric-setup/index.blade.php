<x-base-layout>
    <div class="container-fluid">



        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ url("dashboard/department/section-setup/kpi/100/index/{$kpiId}", ) }}">Setup For
                            Section</a> > Metrics
                        {{--  > <a href="#">{{ session('section_metric_name') }}</a>  --}}
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div>
            <form action="{{ url("dashboard/department/section/metric-setup/kpi/{$kpiId}/section/{$sectionScore}/create/{$sectionId}") }}" method="GET">
                @csrf
                <button type="submit" class="btn btn-success btn-rounded waves-effect waves-light "><i
                        class="bx bxs-plus"></i>Create
                    Metric</button>
            </form>
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
                                <div class="d-flex gap-2">
                                    <p class="font-bold">Total Section Score Required: <span
                                            class="badge rounded-pill bg-dark">{{ $sectionScore }}</span> </p>
                                    <p class="font-bold">Total Score For Sections Created: <span
                                            class="badge rounded-pill bg-primary">{{ $totalMetricScore }}</span> </p>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>
                                        <th class="align-middle">Metric’s Section Name</th>
                                        <th class="align-middle">Metric Name</th>
                                        <th class="align-middle">Metric Score</th>
                                        <th class="align-middle">Metric Description</th>
                                        {{--  <th class="align-middle">Created At</th>  --}}
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($metrics as $metric)
                                        <tr>
                                            <td><a href="#">{{ $metric->section->name }}</a></td>
                                            <td><a href="#">{{ $metric->name }}</a></td>
                                            <td>{{ $metric->score }}</td>
                                            <td>{{ $metric->description }}</td>
                                            {{--  <td>{{ Carbon\Carbon::parse($metric->createdAt)->diffForHumans() }}
                                            </td>  --}}
                                            <td>

                                                <a href="{{ url("dashboard/department/section/kpi/{$kpiId}/section/{$sectionScore}/{$sectionId}/metric-show/{$metric->id}" ) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i> Edit</span>
                                                </a>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $metric->id }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> Delete</span>
                                                </a>

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $metric->id }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                    Metric Deletion</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    delete this Metric?</h4>

                                                                <p class="text-center">Deleting a <b>metric</b> means
                                                                    removing it from the
                                                                    <b>system entirely</b> and you cannot <b>recover</b>
                                                                    it again
                                                                </p>
                                                                <form action="{{ route('delete.metric', $metric->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    {{--  @method('DELETE')  --}}
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
                                                <p>No Metric For Section Created....</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation example" class="mt-3">
                            {{ $metrics->links() }}
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
                        const sectionMetric = row.querySelector('td:nth-child(1) a').textContent.toLowerCase();
                        const metricName = row.querySelector('td:nth-child(2) a').textContent.toLowerCase();
                        const metricScore = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                        const metricDescription = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                        const createdAt = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                        // Check if any of the fields contain the search term
                        if (sectionMetric.includes(searchTerm) || metricName.includes(searchTerm) || metricScore
                            .includes(searchTerm) ||
                            metricDescription.includes(searchTerm) || createdAt.includes(searchTerm)) {
                            row.style.display = ''; // Show the row
                        } else {
                            row.style.display = 'none'; // Hide the row
                        }
                    });
                });
            </script>
        @endpush
        <!-- end w-100 -->
    </div>





</x-base-layout>
