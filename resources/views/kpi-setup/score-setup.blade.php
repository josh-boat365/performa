<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Score Setup</h4>
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
                                        <th class="align-middle">Description</th>
                                        <th class="align-middle">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A</td>
                                        <td>80 - 100</td>
                                        <td>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</td>
                                        <td>
                                            <a href="#">
                                                <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                                        class="bx bxs-pencil"></i>edit</span>
                                            </a>
                                        </td>
                                    </tr>
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
                                <form action="">
                                    <div class="mb-3">
                                        <label class="form-label">Grade</label>
                                        <div>
                                            <input type="text" class="form-control" required
                                                placeholder="Enter grade eg: 'A'" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Grade score</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Min score</label>
                                                <input type="number" class="form-control" required
                                                    placeholder="Enter minimum grade score eg: 80" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Max score</label>
                                                <input type="number" class="form-control" required
                                                    placeholder="Enter maximum grade score eg: 100" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <div>
                                            <input type="text" class="form-control"
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
