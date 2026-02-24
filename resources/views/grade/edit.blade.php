<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('grade.index') }}">Edit Grade Setup</a> >
                        {{ $grade['grade'] }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('update.grade', $grade['id']) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Grade</label>
                                <div>
                                    <input type="text" class="form-control" name="grade" value="{{ $grade['grade'] }}"
                                        placeholder="Enter grade eg: 'A'" />
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Grade score</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Min score</label>
                                        <input type="text" class="form-control" name="minScore"
                                            value="{{ $grade['minScore'] }}"
                                            placeholder="Enter minimum grade score eg: 80" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Max score</label>
                                        <input type="text" class="form-control" name="maxScore"
                                            value="{{ $grade['maxScore'] }}"
                                            placeholder="Enter maximum grade score eg: 100" />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remark</label>

                                <div>
                                    <input type="text" class="form-control" value="{{ $grade['remark'] }}" name="remark"
                                        placeholder="Enter description eg:This is the highest grade score" />
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Grade</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>



    </div>

</x-base-layout>