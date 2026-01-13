<x-base-layout>

    @php

        $accessToken = session('api_token');
        // Fetch user information
        $responseUser = Http::withToken($accessToken)
            ->get('http://192.168.1.200:5124/HRMS/Employee/GetEmployeeInformation');

        // Handle responses
        $user = $responseUser->successful() ? $responseUser->object() : null;

        $supervisorId = $user->id;

    @endphp

    <div class="container-fluid px-1">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18"> <a href="{{ route('supervisor.index') }}">Employee KPIs</a> >
                        Score
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

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex p-3 justify-content-between">
                            <div>
                                <div class="d-flex gap-3 bg-white mb-2">
                                    <div class="">
                                        <span class="mb-2 badge rounded-pill bg-dark">Employee Name</span> <br>
                                        <span
                                            class="mb-2"><strong>{{ $submittedEmployeeGrade->employeeName ?? '----' }}</strong></span>
                                        <br>
                                        <span class="mb-2 badge rounded-pill bg-secondary">Submitted Employee
                                            Grade</span> <br>
                                        <span
                                            class="mb-1"><strong>{{ $submittedEmployeeGrade->totalKpiScore ?? '----' }}</strong></span>
                                        |
                                        <span
                                            class="mb-1"><strong>{{ $submittedEmployeeGrade->grade ?? '----' }}</strong></span>
                                        |
                                        <span
                                            class="mb-1"><strong>{{ $submittedEmployeeGrade->remark ?? '----' }}</strong></span>
                                    </div>
                                </div>

                            </div>
                            <div>
                                <div class="d-flex gap-3 bg-white mb-2">
                                    <div class="">
                                        <span class="mb-2 badge rounded-pill bg-dark">Employee Name</span> <br>
                                        <span
                                            class="mb-2"><strong>{{ $supervisorGradeForEmployee->employeeName ?? '-----' }}</strong></span>
                                        <br>
                                        <span class="mb-2 badge rounded-pill bg-primary">Supervisor Grade For
                                            Employee</span> <br>
                                        <span
                                            class="mb-1"><strong>{{ $supervisorGradeForEmployee->totalKpiScore ?? '----' }}</strong></span>
                                        |
                                        <span
                                            class="mb-1"><strong>{{ $supervisorGradeForEmployee->grade ?? '----' }}</strong></span>
                                        |
                                        <span
                                            class="mb-1"><strong>{{ $supervisorGradeForEmployee->remark ?? '----' }}</strong></span>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
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
                                                                <div class="d-flex gap-3 bg-white p-3 mb-2">
                                                                    <div class="col-md-2">
                                                                        <span class="mb-2 badge rounded-pill bg-secondary">Employee
                                                                            Score
                                                                        </span>
                                                                        <span><strong>{{ optional($section->sectionEmpScore)->sectionEmpScore ?? '' }}</strong></span>
                                                                    </div>
                                                                    <div class="col-md-9">
                                                                        <span class="mb-2 badge rounded-pill bg-secondary">Employee
                                                                            Comment
                                                                        </span>
                                                                        <span><strong>{{ optional($section->sectionEmpScore)->employeeComment ?? '' }}</strong></span>
                                                                    </div>
                                                                </div>


                                                                <span class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                        Score and
                                                                        Comment</strong></span>
                                                                {{-- Supervisor Comment and Score when Supervisor has submitted their
                                                                scores --}}

                                                                <form action="{{ route('supervisor.rating') }}" method="POST"
                                                                    class="section-form">
                                                                    @csrf
                                                                    <div class="d-flex gap-3">
                                                                        <div class="col-md-2">
                                                                            <input class="form-control mb-3 score-input" type="number"
                                                                                name="sectionSupScore" required
                                                                                placeholder="Enter Score" min="0"
                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                max="{{ $section->sectionScore }}"
                                                                                @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                title="The Score can not be more than the section score {{ $section->sectionScore }}"
                                                                                value="{{ optional($section->sectionEmpScore)->sectionSupScore == 0 ? '' : optional($section->sectionEmpScore)->sectionSupScore }}">
                                                                        </div>
                                                                        <div class="col-md-9">
                                                                            <textarea class="form-control mb-3 comment-input"
                                                                                type="text" name="supervisorComment"
                                                                                placeholder="Enter your comments" rows="3"
                                                                                @disabled(isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))>{{ $section->sectionEmpScore->supervisorComment ?? '' }}</textarea>
                                                                        </div>
                                                                        @if (isset($section->sectionEmpScore) && in_array($section->sectionEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                            <div></div>
                                                                        @else
                                                                            <input type="hidden" name="scoreId"
                                                                                value="{{ $section->sectionEmpScore->id ?? '' }}">

                                                                            <button type="submit" style="height: fit-content"
                                                                                class="btn btn-primary">Save</button>
                                                                        @endif
                                                                    </div>
                                                                </form>
                                                            @else
                                                                @foreach ($section->metrics as $metric)
                                                                    <div class="card border border-success" @style(['border-radius: 10px;'])>
                                                                        <div class="card-body" @style(['background-color: #1eff000d'])>
                                                                            <div class="metric-card">
                                                                                <h5>{{ $metric->metricName }} (<span
                                                                                        style="color: #c80f0f">{{ $metric->metricScore }}</span>)
                                                                                </h5>
                                                                                <p>{{ $metric->metricDescription }}</p>

                                                                                <div class="d-flex gap-3 bg-white p-3 mb-2">
                                                                                    <div class="col-md-2">
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-secondary">Employee
                                                                                            Score
                                                                                        </span>
                                                                                        <span><strong>{{ $metric->metricEmpScore->metricEmpScore ?? '' }}</strong></span>
                                                                                    </div>
                                                                                    <div class="col-md-9">
                                                                                        <span
                                                                                            class="mb-2 badge rounded-pill bg-secondary">Employee
                                                                                            Comment
                                                                                        </span>
                                                                                        <span><strong>{{ $metric->metricEmpScore->employeeComment ?? '' }}</strong></span>
                                                                                    </div>
                                                                                </div>

                                                                                <span class="mb-2 badge rounded-pill bg-primary"><strong>Supervisor
                                                                                        Score and
                                                                                        Comment</strong></span>

                                                                                {{-- ==== SUPERVISOR SCORING WITH COMMENT INPUT ==== --}}
                                                                                <form action="{{ route('supervisor.rating') }}"
                                                                                    method="POST" class="ajax-sup-eval-form">
                                                                                    @csrf
                                                                                    <div class="d-flex gap-3">
                                                                                        <div class="col-md-2">
                                                                                            <input class="form-control mb-3" type="number"
                                                                                                name="metricSupScore" min="0" step="0.01"
                                                                                                pattern="\d+(\.\d{1,2})?"
                                                                                                max="{{ $metric->metricScore }}"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                title="The Score can not be more than the metric score {{ $metric->metricScore }}"
                                                                                                placeholder="Enter Score" required
                                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore == 0 ? '' : optional($metric->metricEmpScore)->metricSupScore }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control mb-3" type="text"
                                                                                                name="supervisorComment"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                placeholder="Enter your comments"
                                                                                                rows="3">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                        </div>

                                                                                        <input type="hidden" name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">


                                                                                        <button type="submit" style="height: fit-content"
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

                            @if (
                                    isset($metric->metricEmpScore) &&
                                    in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM', 'COMPLETED'])
                                )
                                <div></div>
                            @else
                                <div class="float-end">
                                    <div class="d-flex gap-3 pagination-controls">
                                        <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
                                        <button id="next-btn" class="btn btn-primary">Next</button>

                                        <button id="submit-btn" type="button" data-bs-toggle="modal" class="btn btn-success"
                                            data-bs-target=".bs-submit-appraisal-modal-lg" id="submitAppraisalButton"
                                            disabled>Submit
                                            Appraisal</button>
                                    </div>
                                </div>

                                <div class="modal fade bs-submit-appraisal-modal-lg" tabindex="-1" role="dialog"
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
                                                    <input type="hidden" name="employeeId" value="{{ $employeeId }}">
                                                    <input type="hidden" name="kpiId" value="{{ $kpi->kpi->kpiId }}">
                                                    <input type="hidden" name="batchId" value="{{ $kpi->kpi->batchId }}">
                                                    <input type="hidden" name="supervisorId" value="{{ $supervisorId }}">
                                                    <input type="hidden" name="status" value="CONFIRMATION">

                                                    {{-- Textarea for supervisor recommendation (optional recommendation
                                                    comment) --}}
                                                    <div class="mb-3">
                                                        <label for="supervisorRecommendation" class="form-label">Supervisor
                                                            Recommendation (Optional)</label>
                                                        <textarea class="form-control" id="supervisorRecommendation"
                                                            name="supervisorRecommendation" rows="4"
                                                            placeholder="Enter your recommendation here..."></textarea>
                                                    </div>

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
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const sections = document.querySelectorAll('.section-tab');
                                        const prevBtn = document.getElementById('prev-btn');
                                        const nextBtn = document.getElementById('next-btn');
                                        const submitBtn = document.getElementById('submit-btn');
                                        const currentPageSpan = document.getElementById('current-page');
                                        const totalPagesSpan = document.getElementById('total-pages');
                                        const progressBar = document.getElementById('progress-bar');

                                        // Use unique key per employee to avoid page persistence across different forms
                                        const currentEmployeeId = '{{ $employeeId }}';
                                        const pageStorageKey = `currentPage_supervisor_${currentEmployeeId}`;
                                        const lastEmployeeKey = 'lastViewedEmployeeId_supervisor';

                                        // Check if we're viewing a different employee - if so, reset to page 0
                                        const lastViewedEmployee = sessionStorage.getItem(lastEmployeeKey);
                                        let currentPage = 0;

                                        if (lastViewedEmployee === currentEmployeeId) {
                                            // Same employee, restore the page
                                            currentPage = parseInt(sessionStorage.getItem(pageStorageKey) || 0);
                                        } else {
                                            // Different employee, start from page 0
                                            sessionStorage.setItem(lastEmployeeKey, currentEmployeeId);
                                            sessionStorage.setItem(pageStorageKey, '0');
                                        }

                                        const sectionsPerPage = 3;
                                        const totalPages = Math.ceil(sections.length / sectionsPerPage);

                                        totalPagesSpan.textContent = totalPages;

                                        // Helper function to show toast messages
                                        function showToast(type, message) {
                                            if (typeof Swal !== 'undefined') {
                                                Swal.fire({
                                                    toast: true,
                                                    icon: type,
                                                    title: message,
                                                    position: 'top-end',
                                                    showConfirmButton: false,
                                                    timer: 3000,
                                                    timerProgressBar: true
                                                });
                                            } else {
                                                // Fallback if SweetAlert2 is not loaded
                                                console.warn('SweetAlert2 not loaded. Message:', message);
                                                alert(message);
                                            }
                                        }

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
                                                const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"], input[type="number"][name*="SupScore"]');
                                                { { --  const commentInputs = sections[i].querySelectorAll('textarea[name*="Comment" ]'); --} }
                                                const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                { { --  const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== ''); --} }

                                                {
                                                    { --  if (!scoresFilled || !commentsFilled) { --} }
                                                    if (!scoresFilled) {
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
                                                    { { --  const commentInputs = section.querySelectorAll('textarea[name*="Comment"]'); --} }
                                                    const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                                                    { { --  const commentsFilled = Array.from(commentInputs).every(input => input.value.trim() !== ''); --} }
                                                    { { --  if (scoresFilled && commentsFilled) totalValid++; --} }
                                                    if (scoresFilled) totalValid++;
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
                                                } if (currentPageSpan) currentPageSpan.textContent = page + 1; sessionStorage.setItem(pageStorageKey, page); updateButtons(); window.scrollTo({ top: sections[start].offsetTop, behavior: 'smooth' });
                                            } if (prevBtn) {
                                                prevBtn.addEventListener('click', function () {
                                                    if (currentPage > 0) {
                                                        currentPage--;
                                                        showPage(currentPage);
                                                    }
                                                });
                                            }

                                            if (nextBtn) {
                                                nextBtn.addEventListener('click', function () {
                                                    if (currentPage < totalPages - 1 && checkInputs(currentPage)) { currentPage++; showPage(currentPage); }
                                                });
                                            } document.querySelectorAll('input[type="number" ], textarea').forEach(input => {
                                                input.addEventListener('input', function () {
                                                    validateField(this);
                                                    updateButtons();
                                                });
                                            });

                                            // Enhanced AJAX form handler with proper error handling
                                            document.querySelectorAll('form.ajax-sup-eval-form, form.section-form, form.ajax-eval-form').forEach(form => {
                                                form.addEventListener('submit', function (e) {
                                                    { { --e.preventDefault(); --} }
                                                    const scrollPos = window.scrollY;
                                                    const formData = new FormData(form);
                                                    const saveBtn = form.querySelector('button[type="submit"]');
                                                    const originalText = saveBtn.innerHTML;

                                                    // Store scroll position and current page state before submission
                                                    sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());
                                                    sessionStorage.setItem(pageStorageKey, currentPage.toString());

                                                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                                                    saveBtn.disabled = true;

                                                    fetch(form.action, {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                                            'Accept': 'application/json'
                                                        },
                                                        body: formData
                                                    })
                                                        .then(response => {
                                                            // Check for 401 status (session expired)
                                                            if (response.status === 401) {
                                                                return response.json().then(data => {
                                                                    if (data.session_expired) {
                                                                        alert('Your session has expired. Please log in again.');
                                                                        window.location.href = data.redirect || '{{ route("login") }}';
                                                                        return null;
                                                                    }
                                                                    throw new Error(data.message || 'Session expired');
                                                                });
                                                            }
                                                            // Check if response is ok (status 200-299)
                                                            if (!response.ok) {
                                                                // Try to parse error message from response
                                                                return response.json().then(data => {
                                                                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                                                                }).catch(() => {
                                                                    throw new Error(`HTTP error! status: ${response.status}`);
                                                                });
                                                            }
                                                            return response.json();
                                                        })
                                                        .then(data => {
                                                            if (!data) return; // Session expired, already redirecting

                                                            // Store the response data for after refresh
                                                            if (data.success) {
                                                                sessionStorage.setItem('showSuccessToast', JSON.stringify({
                                                                    message: data.message || 'Saved successfully'
                                                                }));
                                                            } else {
                                                                sessionStorage.setItem('showErrorToast', JSON.stringify({
                                                                    message: data.message || 'An error occurred'
                                                                }));
                                                            }

                                                            // Force page refresh to get updated data
                                                            window.location.reload();
                                                        })
                                                        .catch(error => {
                                                            console.error('Error:', error);

                                                            // Restore button state
                                                            saveBtn.innerHTML = originalText;
                                                            saveBtn.disabled = false;

                                                            // Show error toast immediately without reload
                                                            showToast('error', error.message || 'An unexpected error occurred. Please try again.');
                                                        });
                                                });
                                            });

                                            // Show the initial page
                                            showPage(currentPage);

                                            // Check for toast messages after page refresh and restore scroll position
                                            setTimeout(() => {
                                                // First, restore scroll position
                                                const savedScrollPos = sessionStorage.getItem('preserveScrollPosition');
                                                if (savedScrollPos) {
                                                    const scrollPos = parseInt(savedScrollPos);
                                                    if (!isNaN(scrollPos)) {
                                                        window.scrollTo({
                                                            top: scrollPos,
                                                            behavior: 'instant'
                                                        });
                                                        console.log(`Scroll position restored to: ${scrollPos}`);
                                                    }
                                                    sessionStorage.removeItem('preserveScrollPosition');
                                                }

                                                // Then show toast messages
                                                const successToast = sessionStorage.getItem('showSuccessToast');
                                                if (successToast) {
                                                    const toastData = JSON.parse(successToast);
                                                    showToast('success', toastData.message);
                                                    sessionStorage.removeItem('showSuccessToast');
                                                }

                                                const errorToast = sessionStorage.getItem('showErrorToast');
                                                if (errorToast) {
                                                    const toastData = JSON.parse(errorToast);
                                                    showToast('error', toastData.message);
                                                    sessionStorage.removeItem('showErrorToast');
                                                }
                                            }, 100);
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