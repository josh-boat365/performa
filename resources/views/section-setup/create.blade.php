<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('section.index') }}">SECTIONS  </a> > Setup For Sections

                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Section Creation </h3>

                    <form action="{{ route('store.section') }}" class="custom-validation" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Name</label>
                            <div class="col-md-12">
                                <textarea class="form-control" name="name" required placeholder="Enter Name for Section" rows="3"
                                    id="example-text-input">{{ old('name') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Description</label>
                            <div class="col-md-12">
                                <textarea class="form-control" name="description" required placeholder="Enter Description for Section" rows="3"
                                    id="example-text-input">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Section Score</label>
                            <div class="col-md-12">
                                <input class="form-control" type="number" name="score" required
                                    value="{{ old('score') }}" id="example-text-input">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="example-text-input" class="">Select KPI For Section to Belong To</label>
                            <div class="col-md-12">
                                <select name="kpiId" class="form-select">
                                    <option>Select KPI</option>

                                    @foreach ($activeKpis as $kpi)
                                        <option value="{{ $kpi->id }}"
                                            {{ old('kpiId') == $kpi->id ? 'selected' : '' }}>
                                            {{ $kpi->name }} - {{ $kpi->empRole->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="active" value="1">

                        <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12 mt-4">
                            Create
                        </button>
                    </form>

                </div>
            </div>
        </div>



    </div>




</x-base-layout>
