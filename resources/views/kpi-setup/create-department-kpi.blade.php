<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Create Department KPI</h4>
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

                        <th>Department KPI</th>
                        <th>Batch</th>
                        <th>Created At</th>

                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><a href="{{ route('create.unit.setup') }}">IT Department</a></th>
                        <td>January 2025 Batch - First Quarter</td>
                        <td>7th October, 2022</td>

                    </tr>
                    <tr>
                        <th scope="row"><a href="{{ route('create.unit.setup') }}">Finanace Department</a></th>
                        <td>January 2025 Batch - Second Quarter</td>
                         <td>7th October, 2025</td>
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
                <h5 id="offcanvasRightLabel">Setup For KPI</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form class="custom-validation" action="">

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Department</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Select Department</option>
                                <option>IT</option>
                                <option>Admin</option>
                                <option>Operations</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="example-text-input" class="">Select Batch</label>
                        <div class="col-md-12">
                            <select name="type" class="form-select">
                                <option>Select Batch</option>
                                <option>January 2025 Batch - First Quarter</option>
                                <option>January 2025 Batch - Second Quarter</option>
                                
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
