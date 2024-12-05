<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Grade Setup</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        {{--  <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>Grade Guidance</h4>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="mb-4 d-flex gap-5">
                        <span class="badge bg-primary rounded-pill">
                            A
                        </span>

                        <h5>Exceptional results, consistently surpassing goals and demonstrating leadership and
                            innovation.</h5>

                    </div>
                    <div class="mb-4 d-flex gap-5">
                        <span class="badge bg-primary rounded-pill">
                            B
                        </span>

                        <h5>Solid performance, occasionally exceeding expectations, with room for minor improvement.
                        </h5>

                    </div>
                    <div class="mb-4 d-flex gap-5">
                        <span class="badge bg-primary rounded-pill">
                            C
                        </span>

                        <h5>Satisfactory work, meeting the basic requirements and objectives of the role.</h5>

                    </div>
                    <div class="mb-4 d-flex gap-5">
                        <span class="badge bg-primary rounded-pill">
                            D
                        </span>

                        <h5>Below satisfactory; needs significant improvement in key areas to meet role requirements.
                        </h5>

                    </div>
                    <div class="mb-4 d-flex gap-5">
                        <span class="badge bg-primary rounded-pill">
                            F
                        </span>

                        <h5>Fails to meet the minimum expectations; requires immediate intervention and improvement.
                        </h5>

                    </div>

                </div>
            </div>
        </div>  --}}

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <div class="search-box me-2 mb-2 d-inline-block">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" autocomplete="off"
                                            id="searchTableList" placeholder="Search...">
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="text-sm-end">
                                    <button type="button" data-bs-toggle="modal" data-bs-target=".bs-example-modal-lg"
                                        class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2 "><i
                                            class="mdi mdi-plus me-1"></i> Add Grade</button>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table align-middle table-hover table-nowrap dt-responsive nowrap w-100 table-check"
                                id="order-list">
                                <thead class="table-light">
                                    <tr>

                                        <th class="align-middle">Grade</th>
                                        <th class="align-middle">Score</th>
                                        <th class="align-middle">Remark</th>
                                        <th class="align-middle">Created At</th>
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($grades as $grade)
                                        <tr>
                                            <td>{{ $grade->grade }}</td>
                                            <td>{{ $grade->minScore }} - {{ $grade->maxScore }}</td>
                                            <td>{{ $grade->remark }}</td>
                                            <td>{{ Carbon\Carbon::parse($grade->createdAt)->diffForHumans() }}
                                            </td>
                                            <td>
                                                <a href="{{ route('show.grade', $grade->id) }}">
                                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                            class="bx bxs-pencil"></i>edit</span>
                                                </a>
                                                {{--  <a href="#" data-bs-toggle="modal"
                                                    data-bs-target=".bs-delete-modal-lg-{{ $grade->id }}">
                                                    <span class="badge rounded-pill bg-danger fonte-size-13"><i
                                                            class="bx bxs-trash"></i> delete</span>
                                                </a>  --}}

                                                <!-- Modal for Delete Confirmation -->
                                                <div class="modal fade bs-delete-modal-lg-{{ $grade->id }}"
                                                    tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                                    Grade
                                                                    Deletion</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h4 class="text-center mb-4">Are you sure you want to
                                                                    delete this
                                                                    Grade?</h4>
                                                                <form action="{{ route('delete.grade', $grade->id) }}"
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
                                                <p>No Grades Created....</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!--  Large modal example -->
                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg ">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="myLargeModalLabel">Large modal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('store.grade') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Grade</label>
                                        <div>
                                            <input type="text" class="form-control" name="grade" required
                                                placeholder="Enter grade eg: 'A'" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Grade score</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Min score</label>
                                                <input type="text" class="form-control" name="minScore" required
                                                    placeholder="Enter minimum grade score eg: 80" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Max score</label>
                                                <input type="text" class="form-control" name="maxScore" required
                                                    placeholder="Enter maximum grade score eg: 100" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Remark</label>
                                        <div>
                                            <input type="text" class="form-control" name="remark"
                                                placeholder="Enter description eg:This is the highest grade score" />
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Grade</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>



    </div>

</x-base-layout>
