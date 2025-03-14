<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="#">Employee KPIs</a> > Score Employee
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="mt-4 mb-4" style="background-color: gray; height: 1px;"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Supervisor Evaluation Form</h4>

                        <div id="pagination-count" class=" text-center mb-3">
                            <span><b>Current Page</b></span>
                            <span class="badge rounded-pill bg-primary" id="current-page">1</span>/ <span><b>Last
                                    Page</b></span><span class="badge rounded-pill bg-dark" id="total-pages">1</span>
                        </div>

                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $index => $kpi)
                                        <div class="kpi">

                                            @foreach ($kpi->activeSections as $sectionIndex => $section)
                                                <div class="card border border-primary section-tab" @style(['border-radius: 10px;'])
                                                    @style(['border-radius: 10px; display: none;'])
                                                    data-section-page="{{ floor($sectionIndex / 3) }}">
                                                    <div class="card-body"
                                                        style=" {{ $section->metrics->isEmpty() ? 'background-color: #0000ff0d;' : '' }}">
                                                        <div class="section-card" style="margin-top: 2rem;">
                                                            <h4 class="card-title">{{ $section->sectionName }} (<span
                                                                    style="color: #c80f0f">{{ $section->sectionScore }}</span>)
                                                            </h4>
                                                            <p>{{ $section->sectionDescription }}</p>

                                                            @if ($section->metrics->isEmpty())
                                                                <div class="d-flex gap-3">
                                                                    <div class="col-md-2">
                                                                        <input class="form-control mb-3 score-input"
                                                                            type="number" name="sectionEmpScore"
                                                                            required placeholder="Enter Score"
                                                                            @disabled(isset($section->sectionEmpScore) &&
                                                                                    in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                                            value="{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment"
                                                                            placeholder="Enter your comments" rows="3" @disabled(isset($section->sectionEmpScore) &&
                                                                                    in_array($section->sectionEmpScore->status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))>{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</textarea>
                                                                    </div>
                                                                </div>


                                                                <span
                                                                    class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                        Score and
                                                                        Comment</strong></span>
                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their scores --}}

                                                                <form action="{{ route('supervisor.rating') }}"
                                                                    method="POST" class="section-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3 score-input"
                                                                                type="number" name="sectionSupScore"
                                                                                required placeholder="Enter Score"
                                                                                min="0"
                                                                                max="{{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                                value="{{ optional($section->sectionEmpScore)->sectionSupScore == 0 ? '' : optional($section->sectionEmpScore)->sectionSupScore }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control mb-3 comment-input" type="text" name="supervisorComment"
                                                                                placeholder="Enter your comments" rows="3" @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))>{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                        </div>
                                                                        @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                            <div></div>
                                                                        @else
                                                                            <input type="hidden" name="scoreId"
                                                                                value="{{ $section->sectionEmpScore->id ?? '' }}">

                                                                            <button type="submit"
                                                                                style="height: fit-content"
                                                                                class="btn btn-primary">Save</button>
                                                                        @endif
                                                                    </div>
                                                                </form>
                                                            @else
                                                                @foreach ($section->metrics as $metric)
                                                                    <div class="card border border-success"
                                                                        @style(['border-radius: 10px;'])>
                                                                        <div class="card-body" @style(['background-color: #1eff000d'])>
                                                                            <div class="metric-card">
                                                                                <h5>{{ $metric->metricName }} (<span
                                                                                        style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                                </h5>
                                                                                <p>{{ $metric->metricDescription }}</p>


                                                                                <div class="d-flex gap-3">
                                                                                    <div class="col-md-2">
                                                                                        <input
                                                                                            class="form-control mb-3 score-input"
                                                                                            type="number"
                                                                                            name="metricEmpScore"
                                                                                            placeholder="Enter Score"
                                                                                            required min="0"
                                                                                            max="{{ $metric->metricScore }}"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION', 'PROBLEM']))
                                                                                            title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                            value="{{ $metric->metricEmpScore->metricEmpScore ?? '' }}">
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <textarea class="form-control mb-3 comment-input" type="text" name="employeeComment" rows="3"
                                                                                            placeholder="Enter your comments" @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['REVIEW', 'CONFIRMATION', 'PROBLEM']))>{{ $metric->metricEmpScore->employeeComment ?? '' }}</textarea>
                                                                                    </div>

                                                                                </div>

                                                                                <span
                                                                                    class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                                        Score and
                                                                                        Comment</strong></span>

                                                                                {{--  ==== SUPERVISOR SCORING WITH COMMENT INPUT ====  --}}
                                                                                <form
                                                                                    action="{{ route('supervisor.rating') }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="number"
                                                                                                name="metricSupScore"
                                                                                                min="0"
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                                placeholder="Enter Score"
                                                                                                required
                                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore == 0 ? '' : optional($metric->metricEmpScore)->metricSupScore }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control mb-3" type="text" name="supervisorComment" @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                placeholder="Enter your comments" rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                        </div>

                                                                                        <input type="hidden"
                                                                                            name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">


                                                                                        <button type="submit"
                                                                                            style="height: fit-content"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                            class="btn btn-primary">Save</button>

                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <p>No KPIs available for this employee.</p>
                                @endif
                            </div>

                            <hr class="mt-10">

                            @if (isset($metric->metricEmpScore) &&
                                    in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
                                <div></div>
                            @else
                                <div class="float-end">
                                    <div class="d-flex gap-3 pagination-controls">
                                        <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
                                        <button id="next-btn" class="btn btn-primary">Next</button>

                                        <button id="submit-btn" type="button" data-bs-toggle="modal"
                                            class="btn btn-success" data-bs-target=".bs-delete-modal-lg"
                                            id="submitAppraisalButton" disabled>Submit Appraisal</button>
                                    </div>
                                </div>

                                <div class="modal fade bs-delete-modal-lg" tabindex="-1" role="dialog"
                                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myLargeModalLabel">Confirm
                                                    Appraisal
                                                    Submit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="text-center mb-4">Are you sure you want to
                                                    <b>Submit</b> employee <b>Appraisal</b> for
                                                    <b>Confirmation</b>?

                                                </h4>
                                                <form action="{{ route('submit.appraisal') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="kpiId"
                                                        value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId"
                                                        value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="status" value="CONFIRMATION">
                                                    <div class="d-grid">

                                                        <button type="submit" id="submitReviewButton"
                                                            class="btn btn-success">Submit
                                                            Employee Appraisal
                                                            For Confirmation</button>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @push('scripts')
                                <script>
                                    // Function to check if all score inputs and comments are filled
                                    function checkInputs() {
                                        const scoreInputs = document.querySelectorAll('input[type="number"][name*="SupScore"]');
                                        const commentInputs = document.querySelectorAll('textarea[name="employeeComment"]');

                                        const allScoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                        const allCommentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== '');

                                        // Enable or disable the submit button based on input values
                                        document.getElementById('submit-btn').disabled = !(allScoresFilled && allCommentsFilled);
                                    }

                                    // Attach event listeners to all score inputs and comment inputs
                                    document.querySelectorAll('input[type="number"][name*="SupScore"], textarea[name="employeeComment"]').forEach(
                                        input => {
                                            input.addEventListener('input', checkInputs);
                                        });

                                    // Initial check in case inputs are pre-filled
                                    checkInputs();
                                </script>


                                {{--  New script for pagination  --}}
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const sections = document.querySelectorAll('.section-tab');
                                        const prevBtn = document.getElementById('prev-btn');
                                        const nextBtn = document.getElementById('next-btn');
                                        const submitBtn = document.getElementById('submit-btn');
                                        const currentPageSpan = document.getElementById('current-page');
                                        const totalPagesSpan = document.getElementById('total-pages');
                                        let currentPage = parseInt(sessionStorage.getItem('currentPage') || 0);
                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        // Initialize Pagination Count
                                        totalPagesSpan.textContent = totalPages;

                                        function checkInputs(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            let allFilled = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="SupScore"]');
                                                const commentInputs = sections[i].querySelectorAll('textarea[name="supervisorComment"]');

                                                const allScoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const allCommentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== '');

                                                if (!allScoresFilled || !allCommentsFilled) {
                                                    allFilled = false;
                                                    break;
                                                }
                                            }

                                            return allFilled;
                                        }

                                        function updateButtons() {
                                            prevBtn.disabled = currentPage === 0;
                                            nextBtn.disabled = !checkInputs(currentPage) || currentPage === totalPages - 1;
                                            submitBtn.disabled = !Array.from({
                                                length: totalPages
                                            }).every((_, page) => checkInputs(page));
                                        }

                                        function showPage(page) {
                                            sections.forEach(section => {
                                                section.style.display = 'none';
                                            });
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            for (let i = start; i < end && i < sections.length; i++) {
                                                sections[i].style.display = 'block';
                                            }

                                            currentPageSpan.textContent = page + 1; // Update current page display
                                            sessionStorage.setItem('currentPage', page); // Save the current page to sessionStorage

                                            updateButtons();
                                        }

                                        prevBtn.addEventListener('click', function() {
                                            if (currentPage > 0) {
                                                currentPage--;
                                                showPage(currentPage);
                                            }
                                        });

                                        nextBtn.addEventListener('click', function() {
                                            if (currentPage < totalPages - 1) {
                                                currentPage++;
                                                showPage(currentPage);
                                            }
                                        });

                                        // Attach event listeners to all score inputs and comment inputs
                                        document.querySelectorAll('input[type="number"][name*="SupScore"], textarea[name="supervisorComment"]')
                                            .forEach(
                                                input => {
                                                    input.addEventListener('input', updateButtons);
                                                });

                                        // Show the page on load
                                        showPage(currentPage);
                                    });
                                </script>
                            @endpush



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end col -->
    </div>



    </div>

</x-base-layout>
