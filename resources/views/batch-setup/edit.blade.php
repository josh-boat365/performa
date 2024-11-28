<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a
                            href="{{ route('batch.setup.index') }}">{{ $batch_data['name'] }}</a> > Update Batch Details
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Appraisal Batch </h3>
                    <form action="{{ route('update.batch', $batch_data['id']) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">KPI Batch Name</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="name" required
                                    value="{{ $batch_data['name'] }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Batch Year</label>
                            <div class="col-md-12">
                                <select name="year" id="yearSelect1" class="form-select">
                                    <option>Select Year</option>
                                    @for ($year = 2024; $year <= 2032; $year++)
                                        <option value="{{ $year }}"
                                            {{ $batch_data['year'] == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                    @endfor

                                </select>
                            </div>
                        </div>
                       
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Batch State</label>
                            <div class="col-md-12">
                                <select class="form-control" id="stateSelect" name="active" onchange="updateState()">
                                    <option value="1" {{ $batch_data['active'] === true ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $batch_data['active'] === false ? 'selected' : '' }}>
                                        Deactivate</option>
                                </select>

                                <input type="hidden" name="stateHidden" id="stateHidden"
                                    value="{{ $batch_data['active'] }}">

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
