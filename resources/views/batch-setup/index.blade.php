<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Appraisal Batch Setup For KPIs</h4>
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

        {{--  <div class="row">
            <div class="col-md-12">
                <div class="card card-body">
                    <h3 class="card-title">Special title treatment</h3>
                    <p class="card-text">With supporting text below as a natural lead-in to additional
                        content.</p>
                    <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light">Go somewhere</a>
                </div>
            </div>
        </div>  --}}

        <div class="table-responsive">
            <table class="table table-borderless table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Batch Name</th>
                        <th>Batch Short Name</th>
                        <th>Period</th>
                        <th>Created at</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse ($batches as $batch)
                        <tr>
                            <th scope="row"><a href="#">{{ $batch['name'] }}</a></th>
                            <td>{{ $batch['shortName'] }}</td>
                            <td>{{ $batch['period'] }} (Months)</td>
                            <td>{{ Carbon\Carbon::parse($batch['createdAt'])->format('jS F, Y : g:i A') }}</td>
                            <td>
                                @if ($batch['status'] == 1)
                                    <span class="badge rounded-pill bg-success">Active</span>
                                @else
                                    <span class="badge rounded-pill bg-dark">Deactivated</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('show.batch', $batch['id']) }}">
                                    <span class="badge rounded-pill bg-primary fonte-size-13"><i
                                            class="bx bxs-pencil"></i>edit</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <p>No Batch Created....</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <nav aria-label="Page navigation example" class="mt-3">
                {{ $batches->links() }}
            </nav>
        </div>



        <!-- right offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Batch Setup For Appraisal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('create.batch') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <label for="example-text-input" class="">KPI Batch Name</label>
                        <div class="col-md-12">
                            <input class="form-control" type="text" name="name" required
                                value="{{ old('name') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="example-text-input" class="">Period</label>
                        <div class="col-md-12">
                            <input class="form-control" type="number" name="period" required
                                value="{{ old('period') }}" id="example-text-input">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="example-text-input" class="">Batch Year</label>
                        <div class="col-md-12">
                            <select name="year" id="yearSelect" class="form-select">
                                <option>Select Year</option>
                                @for ($year = 2024; $year <= 2032; $year++)
                                    <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    {{--  <input type="hidden" name="shortName" value="A1H2025">  --}}
                    <button type="submit" class="btn btn-primary waves-effect waves-light col-md-12  mt-4">
                        Create
                    </button>
                </form>

            </div>
        </div>

    </div>




</x-base-layout>
