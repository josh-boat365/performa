<x-base-layout>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ route('kpi.setup') }}">Setup For KPIs</a> > Section Setup
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>


        <form action="#">
            <div class=" d-flex">
                <div class="w-100">
                    <div class="w-100">
                        <div class="d-md-flex">

                            <!-- filemanager-leftsidebar -->

                            <div class="w-100">
                                <div class="card">
                                    <div class="card-body">
                                        <div>
                                            <div class="row mb-3">
                                                <div class="col-xl-3 col-sm-6">
                                                    <div class="mt-2">
                                                        <h5>Section Configuration</h5>
                                                    </div>
                                                </div>
                                                <div class="col-xl-9 col-sm-6">
                                                    <div class="mt-4 mt-sm-0 float-sm-end d-flex align-items-center">
                                                        {{--  Section header  --}}
                                                        <div class="dropdown mb-0">
                                                            <a class="btn btn-link text-muted" role="button"
                                                                data-bs-toggle="dropdown" aria-haspopup="true">
                                                                <i class="bx bxs-cog"></i>
                                                                Settings
                                                            </a>

                                                            <div class="dropdown-menu dropdown-menu-end"
                                                                style="padding: 10px !important; width:28vh;">

                                                                <div>
                                                                    <input data-bs-toggle="collapse"
                                                                        data-bs-target="#collapseExample"
                                                                        aria-expanded="false"
                                                                        aria-controls="collapseExample"
                                                                        class="form-check-input" type="checkbox"
                                                                        id="formCheckcolor1">
                                                                    <label class="form-check-label"
                                                                        for="formCheckcolor1">
                                                                        Enable Section Metric
                                                                    </label>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            {{--  Section setup  --}}
                                            <div class="">
                                                <form action="" class="repeater">
                                                    <div data-repeater-list="group-b">
                                                        <div data-repeater-item class="d-flex gap-3 p-2">
                                                            <div class="mb-3 col-lg-10">
                                                                <label for="email">Section Name</label>
                                                                <input type="text" id="description"
                                                                    class="form-control"
                                                                    placeholder="Enter Description" />
                                                            </div>
                                                            <div class="mb-3 col-sm-2">
                                                                <label for="name">Score</label>
                                                                <input type="number" id="score" name="score"
                                                                    class="form-control" placeholder="Section Score" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="collapse" id="collapseExample">
                                                <div class="mt-4 mb-4" style="background-color: gray; height: 1px;">
                                                </div>
                                                {{--  Section Metric Setup  --}}
                                                {{--  Section Metric Header  --}}
                                                <h6>Section Metric Configuration</h6>
                                                <div class="">
                                                    <form action="" class="repeater">
                                                        <div data-repeater-list="group-b">
                                                            <div data-repeater-item class="d-flex justify-center gap-3">

                                                                <div class="mb-3 col-lg-8">
                                                                    <label for="email">Metric Name</label>
                                                                    <input type="text" id="description"
                                                                        class="form-control"
                                                                        placeholder="Enter Description" />
                                                                </div>
                                                                <div class="mb-3 col-sm-2">
                                                                    <label for="name">Score</label>
                                                                    <input type="number" id="score" name="score"
                                                                        class="form-control"
                                                                        placeholder="Section Score" />
                                                                </div>
                                                                <div class=" col-sm-2 align-self-center">
                                                                    <div class="p-3 mt-2">
                                                                        <input data-repeater-delete type="button"
                                                                            class="btn btn-danger" value="Delete" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-3">
                                                            <input data-repeater-create type="button"
                                                                class="btn btn-primary" value="Add" />
                                                            <button type="submit" class="btn btn-success">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end w-100 -->
                        </div>
                    </div>
                </div>

                <div class="card ms-lg-2" style="height: fit-content">
                    <div class="card-body">

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-success">Create section</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>

</x-base-layout>
