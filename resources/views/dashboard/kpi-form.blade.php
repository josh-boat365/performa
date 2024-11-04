<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Available KPIs For You</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="col-md-12">
            <div class="card card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="badge rounded-pill bg-dark">Number of KPIs</span>
                        <span class="badge rounded-pill bg-primary">16</span>


                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"> Save</button>
                        <button class="btn btn-success"> Submit</button>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar progress-bar-animated progress-bar-striped" role="progressbar"
                            style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="kpi-form" role="tabpanel">
                                <form action="#">

                                    <div class="container">
                                        @foreach ($kpis as $kpi)
                                            <hr>
                                            <div class="row mb-3">
                                                <div class="col-4">
                                                    <h4>Team Work <span class="badge rounded-pill bg-dark">80:
                                                            {{ $kpi['id'] }}</span></h4>
                                                </div>

                                            </div>
                                            <div class="row border-bottom mb-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="font-size-13"><b>Communication Skills</b> <span
                                                            class="badge rounded-pill bg-dark">5</span></div>
                                                    <div class=" align-items-center" style="margin-top: 1.8rem">
                                                        <div>
                                                            <p>Good construction of the use of English</p>
                                                        </div>
                                                        <div>
                                                            <p>Effectiveness in expressing ideas clearly</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2 mb-3">

                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Self Rating" />
                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Self Rating" />
                                                    <input type="number" class="form-control"
                                                        placeholder="Self Rating" />
                                                </div>
                                                <div class="col-12 col-md-2">

                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Supervisor's Rating" />
                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Supervisor's Rating" />
                                                    <input type="number" class="form-control"
                                                        placeholder="Supervisor's Rating" />
                                                </div>
                                                <div class="col-12 col-md-2">

                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Manager's Rating" />
                                                    <input type="number" class="form-control mb-2"
                                                        placeholder="Manager's Rating" />
                                                    <input type="number" class="form-control"
                                                        placeholder="Manager's Rating" />
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="mb-2 items-center">
                                                        <span class="font-bold">Comments</span>
                                                        <span class="badge rounded-pill bg-primary">+ Add Comment</span>
                                                    </div>
                                                    <div class="">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter comments" />
                                                        <div class="float-end">
                                                            <span class="mb-2 badge rounded-pill bg-primary">Employee's
                                                                comment</span>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter comments" />
                                                        <div class="float-end">
                                                            <span class="mb-2 badge rounded-pill bg-success">Supervisor's
                                                                comment</span>
                                                        </div>
                                                    </div>
                                                    <div class="">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter comments" />
                                                        <div class="float-end">
                                                            <span class="mb-2 badge rounded-pill bg-warning">Manager's
                                                                comment</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach


                                        {{ $kpis->links() }}
                                        <div class="d-flex gap-3 float-end">
                                            <button class="btn btn-primary">Previous</button>
                                            <button class="btn btn-success">Next</button>
                                        </div>

                                    </div>



                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>


    </div>

</x-base-layout>
