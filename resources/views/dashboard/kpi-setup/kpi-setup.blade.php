<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Setup For KPIs</h4>
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
                        <th>KPI Name</th>
                        <th>score(%)</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><a href="#">Team work</a></th>
                        <td>100</td>
                        <td>7th October, 2022</td>
                        <td>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Deactived</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><a href="#">Learning and Development</a></th>
                        <td>80</td>
                        <td>20th April, 2024</td>
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
                <h5 id="offcanvasRightLabel">Setup For KPI</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form class="custom-validation" action="">
                    <div class="row mb-2">
                        <label for="example-text-input" class="">KPI Name</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="name" required
                                value="{{ old('name') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="example-text-input" class="">Score(%)</label>
                        <div class="col-md-12">
                            <input type="number" class="form-control" name="score" required
                                value="{{ old('score') }}" id="example-text-input">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12">
                        <i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> Loading
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-base-layout>
