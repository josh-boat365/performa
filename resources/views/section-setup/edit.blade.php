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

        {{--  {{ dd($sectionData) }}  --}}

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">KPI Section </h3>
                    {{--  <form action="{{ route('update.section', $sectionData->id) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Name</label>
                            <div class="col-md-12">

                                <textarea class="form-control" name="name" required rows="3" id="example-text-input">{{ $sectionData->name }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select KPI For Section to Belong To</label>
                            <div class="col-md-12">
                                <select name="kpiId" class="form-select">
                                    <option>Select KPI</option>

                                    @foreach ($activeKpis as $kpi)
                                        <option value="{{ $kpi->id }}"
                                            {{ old('kpiId') == $sectionData->kpi->id ? 'selected' : '' }}>
                                            {{ $kpi->name }} - {{ $kpi->empRole->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <textarea class="form-control" name="description" required rows="3" id="example-text-input">{{ $sectionData->description }}</textarea>
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
                    </form>  --}}

                    <form id="updateSectionForm" method="POST"
                        action="{{ route('update.section', $sectionData->id) }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Name</label>
                            <div class="col-md-12">
                                <textarea class="form-control" name="name" required rows="3">{{ $sectionData->name }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="kpiSelect" class="">Select KPI</label>
                            <div class="col-md-12">
                                <select class="form-control" id="kpiSelect" name="kpiId" required>
                                    @foreach ($activeKpis as $kpi)
                                        <option value="{{ $kpi->id }}"
                                            {{ $sectionData->kpi->id === $kpi->id ? 'selected' : '' }}>
                                            {{ $kpi->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="sectionScoreInput" class="">Section Score</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="score" id="sectionScoreInput"
                                    required value="{{ $sectionData->score }}">
                                <div id="error-message" class="text-danger mt-2"></div> <!-- Error message display -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Description</label>
                            <div class="col-md-12">
                                <textarea class="form-control" name="description" required rows="3">{{ $sectionData->description }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="stateSelect" class="">Section State</label>
                            <div class="col-md-12">
                                <select class="form-control" id="stateSelect" name="active" onchange="updateState()">
                                    <option value="1" {{ $sectionData->active === true ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ $sectionData->active === false ? 'selected' : '' }}>
                                        Deactivate</option>
                                </select>
                                <input type="hidden" name="stateHidden" id="stateHidden"
                                    value="{{ $sectionData->active }}">
                            </div>
                        </div>
                        <button type="submit"
                            class="btn btn-success waves-effect waves-light col-md-12 mt-4">Update</button>
                    </form>

                    {{--  <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('updateSectionForm');
                            const scoreInput = document.getElementById('sectionScoreInput');
                            const errorMessageDiv = document.getElementById('error-message');

                            form.addEventListener('submit', function(event) {
                                event.preventDefault(); // Prevent default form submission

                                const formData = new FormData(form);
                                const kpiId = document.getElementById('kpiSelect').value; // Get selected KPI ID
                                const sectionScore = parseFloat(scoreInput.value) || 0; // Get current input value

                                // Optional: Validate score against KPI score before sending request
                                // You can fetch the KPI score dynamically if needed
                                // For now, let's assume the KPI score is 100
                                const kpiScore = 100; // Replace with actual KPI score if available

                                if (sectionScore > kpiScore) {
                                    errorMessageDiv.textContent =
                                        `Section score cannot exceed the KPI score of ${kpiScore}.`;
                                    scoreInput.style.borderColor = 'red'; // Highlight input
                                    return; // Stop further execution
                                } else {
                                    scoreInput.style.borderColor = ''; // Reset border color
                                    errorMessageDiv.textContent = ''; // Clear previous error message
                                }

                                fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 400) {
                                            errorMessageDiv.textContent = data
                                                .response; // Display error message from API
                                            scoreInput.style.borderColor = 'red'; // Highlight input
                                        } else {
                                            // Handle success (e.g., redirect or show success message)
                                            alert('Section updated successfully!');
                                            // Optionally, you can redirect or update the UI accordingly
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        errorMessageDiv.textContent =
                                            'An unexpected error occurred. Please try again later.',
                                            error; // Display a generic error message
                                    });
                            });
                        });
                    </script>  --}}


                </div>
            </div>
        </div>



    </div>




</x-base-layout>
