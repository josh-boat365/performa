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
                        <th>Setup Name</th>
                        <th>Period</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><a href="#">January 2023 Batch</a></th>
                        <td>3(Months)</td>
                        <td>7th October, 2022</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><a href="#">March 2025 Batch</a></th>
                        <td>4(Months)</td>
                        <td>20th April, 2024</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><a href="#">June 2026 Batch</a></th>
                        <td>6(Months)</td>
                        <td>20th August, 2026</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
            <nav aria-label="Page navigation example" class="mt-3">
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>



        <!-- right offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Batch Setup For Appraisal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="">
                    <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Batch Name</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="setup_name" required
                                value="{{ old('setup_name') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="example-text-input" class="">Period</label>
                        <div class="col-md-12">
                            <input class="form-control" type="number" name="duration" required
                                value="{{ old('duration') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="example-text-input" class="">Batch Year</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Select Year</option>
                                <option>2024</option>
                                <option>2025</option>
                                <option>2026</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12  mt-4">
                        Create
                    </button>
                </form>
            </div>
        </div>

    </div>




</x-base-layout>
