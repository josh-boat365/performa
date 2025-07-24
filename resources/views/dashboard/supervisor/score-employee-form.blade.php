<x-base-layout>

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('supervisor.index') }}">Employee KPIs</a> > Score
                        Employee
                    </h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="progress fixed-top" style="height: 10px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>


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
                                                                                min="0" step="0.01"
                                                                                pattern="\d+(\.\d{1,2})?"
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
                                                                                            step="0.01"
                                                                                            pattern="\d+(\.\d{1,2})?"
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
                                                                                    method="POST"
                                                                                    class="ajax-sup-eval-form">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input
                                                                                                class="form-control mb-3"
                                                                                                type="number"
                                                                                                name="metricSupScore"
                                                                                                min="0"
                                                                                                step="0.01"
                                                                                                pattern="\d+(\.\d{1,2})?"
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
                                                    <input type="hidden" name="employeeId"
                                                        value="{{ $employeeId }}">
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
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const sections = document.querySelectorAll('.section-tab');
                                        const prevBtn = document.getElementById('prev-btn');
                                        const nextBtn = document.getElementById('next-btn');
                                        const submitBtn = document.getElementById('submit-btn');
                                        const currentPageSpan = document.getElementById('current-page');
                                        const totalPagesSpan = document.getElementById('total-pages');
                                        const progressBar = document.getElementById('progress-bar');
                                        let currentPage = parseInt(sessionStorage.getItem('currentPage') || 0);
                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        totalPagesSpan.textContent = totalPages;

                                        function validateField(field) {
                                            const value = field.value.trim();
                                            if (value === '') {
                                                field.classList.add('is-invalid');
                                                field.classList.remove('is-valid');
                                                field.closest('.section-tab')?.classList.add('border-danger');
                                                return false;
                                            } else {
                                                field.classList.remove('is-invalid');
                                                field.classList.add('is-valid');
                                                field.closest('.section-tab')?.classList.remove('border-danger');
                                                return true;
                                            }
                                        }

                                        function checkInputs(page) {
                                            const start = page * sectionsPerPage;
                                            const end = start + sectionsPerPage;
                                            let allFilled = true;

                                            for (let i = start; i < end && i < sections.length; i++) {
                                                const scoreInputs = sections[i].querySelectorAll(
                                                    'input[type="number"][name*="EmpScore"], input[type="number"][name*="SupScore"]');
                                                const commentInputs = sections[i].querySelectorAll('textarea[name*="Comment"]');

                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== '');

                                                if (!scoresFilled || !commentsFilled) {
                                                    allFilled = false;
                                                    sections[i].classList.add('border-danger');
                                                } else {
                                                    sections[i].classList.remove('border-danger');
                                                }
                                            }

                                            return allFilled;
                                        }

                                        function updateProgressBar() {
                                            let totalValid = 0;
                                            sections.forEach(section => {
                                                const scoreInputs = section.querySelectorAll(
                                                    'input[type="number"][name*="EmpScore"], input[type="number"][name*="SupScore"]'
                                                );
                                                const commentInputs = section.querySelectorAll('textarea[name*="Comment"]');
                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !==
                                                    '');
                                                if (scoresFilled && commentsFilled) totalValid++;
                                            });
                                            const percent = Math.round((totalValid / sections.length) * 100);
                                            progressBar.style.width = percent + '%';
                                            progressBar.setAttribute('aria-valuenow', percent);
                                            progressBar.textContent = percent + '%';
                                        }

                                        function updateButtons() {
                                            if (prevBtn) prevBtn.disabled = currentPage === 0;
                                            if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1 || !checkInputs(currentPage);
                                            if (submitBtn) submitBtn.disabled = !Array.from({
                                                length: totalPages
                                            }).every((_, i) => checkInputs(i));
                                            updateProgressBar();
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

                                            if (currentPageSpan) currentPageSpan.textContent = page + 1;
                                            sessionStorage.setItem('currentPage', page);
                                            updateButtons();
                                            window.scrollTo({
                                                top: sections[start].offsetTop,
                                                behavior: 'smooth'
                                            });
                                        }

                                        if (prevBtn) {
                                            prevBtn.addEventListener('click', function() {
                                                if (currentPage > 0) {
                                                    currentPage--;
                                                    showPage(currentPage);
                                                }
                                            });
                                        }

                                        if (nextBtn) {
                                            nextBtn.addEventListener('click', function() {
                                                if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                                                    currentPage++;
                                                    showPage(currentPage);
                                                }
                                            });
                                        }

                                        document.querySelectorAll('input[type="number"], textarea').forEach(input => {
                                            input.addEventListener('input', function() {
                                                validateField(this);
                                                updateButtons();
                                            });
                                        });

                                        // Only handle AJAX for supervisor scoring forms
                                        document.querySelectorAll('form.ajax-sup-eval-form, form.section-form').forEach(form => {
                                            form.addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                const scrollPos = window.scrollY;
                                                const formData = new FormData(form);
                                                const saveBtn = form.querySelector('button[type="submit"]');
                                                const originalText = saveBtn.innerHTML;

                                                saveBtn.innerHTML =
                                                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                                                saveBtn.disabled = true;

                                                fetch(form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'X-CSRF-TOKEN': document.querySelector(
                                                                'meta[name="csrf-token"]').getAttribute('content')
                                                        },
                                                        body: formData
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        smoothScroll(form);
                                                    })
                                                    .catch(error => console.error('Error:', error))
                                                    .finally(() => {
                                                        window.scrollTo(0, scrollPos);
                                                        saveBtn.innerHTML = originalText;
                                                        saveBtn.disabled = false;
                                                        updateButtons();
                                                    });
                                            });
                                        });

                                        function smoothScroll(targetForm) {
                                            $('html, body').animate({
                                                scrollTop: $(targetForm).offset().top
                                            }, 500);
                                        }

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
