<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('global.weight.index') }}">GLOBAL WEIGHTS </a> >  Edit Global Weight For Department

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Weight KPI </h3>



                    <form action="{{ route('update.global.weight', $globalWeight['id']) }}" class="custom-validation" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Global KPI</label>
                            <div class="col-md-12">
                                <select name="kpiId" class="form-select">
                                    <option>Select Global KPI</option>

                                    @foreach ($activeKpis as $kpi)
                                        <option value="{{ $kpi['id'] }}"
                                            {{ $kpi['id'] === $globalWeight['kpi']['id'] ? 'selected' : '' }}>
                                            {{ $kpi['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Department</label>
                            <div class="col-md-12">
                                <select name="departmentId" class="form-select">
                                    <option>Select Department</option>

                                    @foreach ($departments as $department)
                                        <option value="{{ $department['id'] }}"
                                            {{ $globalWeight['department']['id'] == $department['id'] ? 'selected' : '' }}>{{ $department['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                         <div class="row mb-3">
                            <label for="example-text-input" class="">Weight Score </label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="weight" required
                                    placeholder="Enter Weighted Score For Department" value="{{ $globalWeight['weight'] }}"
                                    id="example-text-input">
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                            Update
                        </button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
