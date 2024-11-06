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
                            <label for="example-text-input" class="">KPI Batch Short Name</label>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="shortName" required
                                    value="{{ $batch_data['shortName'] }}" id="example-text-input">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Period</label>
                            <div class="col-md-12">
                                <input class="form-control" type="number" name="period" required
                                    value="{{ $batch_data['period'] }}" id="example-text-input">
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
                            <label for="example-text-input" class="">Batch Status</label>
                            <div class="col-md-12">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault"
                                         @if ($batch_data['status'] == 1) checked @endif
                                        onchange="document.getElementById('statusHidden').value = this.checked ? 1 : 0; document.getElementById('switchStatus').textContent = this.checked ? 'Active' : 'Deactivated'">
                                    <label class="form-check-label" for="flexSwitchCheckDefault" id="switchStatus">
                                        @if ($batch_data['status'] == 1)
                                            Active
                                        @else
                                            Deactivated
                                        @endif
                                    </label>
                                </div>
                                <input type="hidden" name="status" id="statusHidden"
                                    value="{{ $batch_data['status'] == 1 ? 1 : 0 }}">

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
