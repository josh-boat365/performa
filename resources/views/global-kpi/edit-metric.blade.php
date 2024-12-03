<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="{{ route('global.metric.index') }}">{{ $metricData->name }}</a> >
                        Update Global Metric Details

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Global Metric </h3>
                    <form action="{{ route('update.metric', $metricData->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Metric Name</label>
                            <div class="col-md-12">

                                <textarea class="form-control" name="name" required placeholder="Enter Name for Section" rows="3"
                                    id="example-text-input">{{ $metricData->name }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Metric Score</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="score" required
                                    value="{{ $metricData->score }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Metric Description</label>
                            <div class="col-md-12">
                                <textarea class="form-control" name="description" required placeholder="Enter Name for Section" rows="3"
                                    id="example-text-input">{{ $metricData->description }}</textarea>

                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select Section For Metric to Belong
                                To</label>
                            <div class="col-md-12">
                                <select name="sectionId" class="form-select">
                                    <option>Select Section</option>

                                    @foreach ($activeSections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ $section->id === $metricData->section->id ? 'selected' : '' }}>
                                            {{ $section->name }} - {{ $section->kpi->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Metric State</label>
                            <div class="col-md-12">
                                <select class="form-control" id="stateSelect" name="active" onchange="updateState()">
                                    <option value="1" {{ $metricData->active === true ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ $metricData->active === false ? 'selected' : '' }}>
                                        Deactivate</option>
                                </select>

                                <input type="hidden" name="stateHidden" id="stateHidden"
                                    value="{{ $metricData->active }}">

                                <script>
                                    function updateState() {
                                        var select = document.getElementById('stateSelect');
                                        var hiddenInputx = document.getElementById('stateHidden');
                                        hiddenInputx.value = select.value === '1';
                                    }
                                </script>
                            </div>
                        </div>
                        <input type="hidden" name="sectionId" value="{{ $metricData->section->id }}">
                        <button type="submit"
                            class="btn btn-success waves-effect waves-light col-md-12 mt-4">Update</button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
