<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('kpi.index') }}">{{ $kpi_data['name'] }}</a> >
                        Update KPI Details

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">KPI Information </h3>

                    <form action="{{ route('update.kpi', $kpi_data['id']) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">KPI Name</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="name" required
                                    value="{{ $kpi_data['name'] }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Description</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="description" required
                                    value="{{ $kpi_data['description'] }}" id="example-text-input">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select KPI Type</label>
                            <div class="col-md-12">
                                <select name="type" class="form-select">
                                    <option>Select KPI type</option>
                                    <option value="REGULAR" {{ $kpi_data['type'] == 'REGULAR' ? 'selected' : '' }}> REGULAR
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Role</label>
                            <div class="col-md-12">
                                <select name="empRoleId" class="form-select">
                                    <option>Select Role</option>

                                    @foreach ($uniqueRoles as $role)
                                        <option value="{{ $role['id'] }}"
                                            {{ $role['id'] == $kpi_data['empRole']['id'] ? 'selected' : '' }}>
                                            {{ $role['name'] }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>



                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Batch</label>
                            <div class="col-md-12">
                                <select name="batchId" class="form-select">
                                    <option>Select Batch</option>
                                    @foreach ($batch_data as $batch)
                                        <option value="{{ $batch['id'] }}"
                                            {{ $batch['id'] == $kpi_data['batch']['id'] ? 'selected' : '' }}>
                                            {{ $batch['name'] }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">KPI State</label>
                            <div class="col-md-12">
                                <select class="form-control" id="stateSelect" name="active" onchange="updateState()">
                                    <option value="1" {{ $kpi_data['active'] === true ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $kpi_data['active'] === false ? 'selected' : '' }}>
                                        Deactivate</option>
                                </select>

                                <input type="hidden" name="stateHidden" id="stateHidden"
                                    value="{{ $kpi_data['active'] }}">

                                <script>
                                    function updateState() {
                                        var select = document.getElementById('stateSelect');
                                        var hiddenInputx = document.getElementById('stateHidden');
                                        hiddenInputx.value = select.value === '1';
                                    }
                                </script>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-success waves-effect waves-light col-md-12 mt-4">Update</button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
