<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Overview of Batches of KPIs</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Your Kpi Grades</h4>

                        <div id="column_chart_datalabel" data-colors='["--bs-primary"]' class="apex-charts"
                            dir="ltr"></div>
                    </div>
                </div><!--end card-->
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Participated KPIs</h4>
                        <span class="">4</span>

                    </div>
                </div><!--end card-->
            </div>
        </div>



    </div>




</x-base-layout>
