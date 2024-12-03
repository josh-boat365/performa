<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('global.index') }}">GLOBAL KPIS </a> > Setup For Global Kpis

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Appraisal KPI </h3>

                    <form action="{{ route('store.global.kpi') }}" class="custom-validation" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">KPI Name</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="name" required
                                    placeholder="Enter Name for KPI" value="{{ old('name') }}"
                                    id="example-text-input">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">KPI Description</label>
                            <div class="col-md-12">
                               <textarea class="form-control" name="description" required placeholder="Enter Description for KPI" rows="3"
                                    id="example-text-input">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select KPI Type</label>
                            <div class="col-md-12">
                                <select name="type" class="form-select">
                                    <option>Select KPI type</option>
                                    <option value="GLOBAL" {{ old('type') == 'GLOBAL' ? 'selected' : '' }}> GLOBAL
                                    </option>
                                    <option value="PROBATION" {{ old('type') == 'PROBATION' ? 'selected' : '' }}>
                                        PROBATION </option>
                                </select>
                            </div>
                        </div>

                         {{--  <div class="row mb-3">
                            <label for="example-text-input" class="">Select Role For KPI to Belong To</label>
                            <div class="col-md-12">
                                <select name="empRoleId" class="form-select">
                                    <option>Select Role</option>

                                    @foreach ($uniqueRoles as $role)
                                        <option value="1"
                                            {{ old('empRoleId') == $role['id'] ? 'selected' : '' }}>
                                            {{ $role['name'] }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>  --}}

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Batch For KPI to Belong To</label>
                            <div class="col-md-12">
                                <select name="batchId" class="form-select">
                                    <option>Select Batch</option>
                                    @foreach ($batch_data as $batch)
                                        <option value="{{ $batch->id }}"
                                            {{ old('batchId') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        {{--  <div class="row mb-3">
                            <label for="example-text-input" class="">Select Department</label>
                            <div class="col-md-12">
                                <select name="departmentId" class="form-select">
                                    <option>Select Department</option>

                                    @foreach ($uniqueDepartments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('departmentId') == $department->id ? 'selected' : '' }}>{{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  --}}

                         {{--  <div class="row mb-3">
                            <label for="example-text-input" class="">Weight Score </label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="global_score" required
                                    placeholder="Enter Weighted Score For Department" value="{{ old('description') }}"
                                    id="example-text-input">
                            </div>
                        </div>  --}}

                        <input type="hidden" name="active" value="1">
                        <input type="hidden" name="empRoleId" value="1">

                        <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                            Create
                        </button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
