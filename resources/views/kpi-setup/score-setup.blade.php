<x-base-layout>

    <div class="container-fluid px-5">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> Score Setup</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="" action="POST" class="repeater">
                            <div data-repeater-list="group-a">
                                <div data-repeater-item class="row">
                                    <div class="mb-3 col-lg-2">
                                        <label for="name">Score</label>
                                        <input type="number" id="score" name="score" class="form-control"
                                            placeholder="Enter Score Value" />
                                    </div>

                                    <div class="mb-3 col-lg-8">
                                        <label for="email">Description</label>
                                        <input type="text" id="description" class="form-control"
                                            placeholder="Enter Description" />
                                    </div>

                                    <div class="col-lg-2 align-self-center">
                                        <div class="d-grid mt-2">
                                            <input data-repeater-delete type="button" class="btn btn-primary"
                                                value="Delete" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex gap-3">
                                <input data-repeater-create type="button" class="btn btn-primary"
                                    value="Add" />
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </div>

</x-base-layout>
