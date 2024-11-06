<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"><a href="{{ route('create.dep.kpi') }}">IT Department</a> > Create Role/Unit KPI</h4>
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


        <div class="table-responsive">
            <table class="table table-borderless table-hover mb-0">

                <thead class="table-light">
                    <tr>

                        <th>Role/Unit KPI</th>
                        <th>Created At</th>
                        <th>Status</th>

                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><a href="{{ route('kpi.setup') }}">Application Surport</a></th>
                        <td>7th October, 2022</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row"><a href="{{ route('kpi.setup') }}">Infrastructure</a></th>
                        <td>7th October, 2024</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
            {{--  Paginator  --}}
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
                <h5 id="offcanvasRightLabel">Setup Unit/Role KPI</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form class="custom-validation" action="">

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Role/Unit In Department</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Select Role/Unit In Department</option>
                                <option>Application Support</option>
                                <option>Infrastructure</option>
                                <option>Manager</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Supervisor</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Eric - IT Head</option>
                                <option>David - IT Manager</option>
                            </select>
                        </div>
                    </div>




                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                         Create
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-base-layout>
