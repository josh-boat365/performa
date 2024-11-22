<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="{{ route('section.index') }}">{{ $sectionData->name }}</a> >
                        Update Section Details

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">KPI Section </h3>
                    <form action="{{ route('update.section', $sectionData->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Name</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="name" required
                                    value="{{ $sectionData->name }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Score</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="score" required
                                    value="{{ $sectionData->score }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Description</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="description" required
                                    value="{{ $sectionData->description }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section State</label>
                            <div class="col-md-12">
                                <select class="form-control" id="stateSelect" name="active" onchange="updateState()">
                                    <option value="1" {{ $sectionData->active === true ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $sectionData->active === false ? 'selected' : '' }}>
                                        Deactivate</option>
                                </select>

                                <input type="hidden" name="stateHidden" id="stateHidden"
                                    value="{{ $sectionData->active }}">

                                <script>
                                    function updateState() {
                                        var select = document.getElementById('stateSelect');
                                        var hiddenInputx = document.getElementById('stateHidden');
                                        hiddenInputx.value = select.value === '1';
                                    }
                                </script>
                            </div>
                        </div>
                        <input type="hidden" name="kpiId" value="{{ $sectionData->kpi->id }}">
                        <button type="submit"
                            class="btn btn-success waves-effect waves-light col-md-12 mt-4">Update</button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
