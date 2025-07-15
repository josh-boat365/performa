<x-base-layout>
    {{--  @push('styles')  --}}
        <style>
            /* Custom styles for appraisal form */
            .section-tab {
                border-radius: 10px;
                display: none;
            }

            .metric-card {
                background-color: #1eff000d;
            }

            .empty-section {
                background-color: #0000ff0d;
            }

            .progress-container {
                height: 10px;
            }

            /* Validation states */
            .is-valid {
                border-color: #198754;
            }

            .is-invalid {
                border-color: #dc3545;
            }
        </style>
    {{--  @endpush  --}}

    <div class="container-fluid px-1">
        <!-- Page Title Section -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        <a href="{{ route('show.batch.kpi', $batchId) }}">My KPIs</a> > Your Appraisal
                    </h4>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress fixed-top progress-container">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
            </div>
        </div>

        <!-- Appraisal Grade Summary -->
        @include('partials.employee.grade-summary', [
            'gradeDetails' => $gradeDetails ?? [],
            'batchId' => $batchId,
        ])

        <div class="mt-4 mb-4 divider"></div>

        <!-- Main Appraisal Form -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Employee Evaluation Form</h4>

                        <!-- Pagination Info (only shown when editable) -->
                        {{--  @if (in_array($kpiStatus, ['PENDING','REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))  --}}
                            <div id="pagination-count" class="text-center mb-3">
                                <span><b>Current Page</b></span>
                                <span class="badge rounded-pill bg-primary" id="current-page">1</span>
                                <span><b>Last Page</b></span>
                                <span class="badge rounded-pill bg-dark" id="total-pages">1</span>
                            </div>
                        {{--  @endif  --}}

                        <!-- Form Content -->
                        <div class="p-3 text-muted">
                            <div id="kpi-form">
                                @if (isset($appraisal) && $appraisal->isNotEmpty())
                                    @foreach ($appraisal as $index => $kpi)
                                        @include('partials.employee.kpi-section', [
                                            'kpi' => $kpi,
                                            'kpiStatus' => $kpiStatus,
                                        ])
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        No KPIs available for this employee.
                                    </div>
                                @endif
                            </div>

                            <hr class="mt-5">

                            <!-- Action Buttons -->
                            @include('partials.employee.action-buttons', [
                                'kpiStatus' => $kpiStatus ?? null,
                                'kpi' => $kpi ?? null,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- CSRF Token Meta Tag -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Appraisal Form Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize CSRF token for AJAX requests
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // DOM Elements
                const sections = document.querySelectorAll('.section-tab');
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                const submitBtn = document.getElementById('submit-btn');
                const currentPageSpan = document.getElementById('current-page');
                const totalPagesSpan = document.getElementById('total-pages');
                const progressBar = document.getElementById('progress-bar');

                // State Management
                let currentPage = parseInt(sessionStorage.getItem('currentPage') || 0);
                const sectionsPerPage = 3;
                const totalPages = Math.ceil(sections.length / sectionsPerPage);
                totalPagesSpan.textContent = totalPages;

                /**
                 * Validate a single form field
                 */
                function validateField(field) {
                    const value = field.value.trim();
                    const isValid = value !== '';

                    field.classList.toggle('is-invalid', !isValid);
                    field.classList.toggle('is-valid', isValid);
                    field.closest('.section-tab')?.classList.toggle('border-danger', !isValid);

                    return isValid;
                }

                /**
                 * Check all inputs on current page
                 */
                function checkInputs(page) {
                    const start = page * sectionsPerPage;
                    const end = start + sectionsPerPage;
                    let allFilled = true;

                    for (let i = start; i < end && i < sections.length; i++) {
                        const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="EmpScore"]');
                        const commentInputs = sections[i].querySelectorAll('textarea[name="employeeComment"]');

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

                /**
                 * Update progress bar based on completion
                 */
                function updateProgressBar() {
                    let totalValid = 0;
                    sections.forEach(section => {
                        const scoreInputs = section.querySelectorAll('input[type="number"][name*="EmpScore"]');
                        const commentInputs = section.querySelectorAll('textarea[name="employeeComment"]');
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

                /**
                 * Update button states based on current page and completion
                 */
                function updateButtons() {
                    prevBtn.disabled = currentPage === 0;
                    nextBtn.disabled = currentPage === totalPages - 1 || !checkInputs(currentPage);
                    submitBtn.disabled = !Array.from({
                        length: totalPages
                    }).every((_, i) => checkInputs(i));
                    updateProgressBar();
                }

                /**
                 * Show a specific page of sections
                 */
                function showPage(page) {
                    // Hide all sections first
                    sections.forEach(section => {
                        section.style.display = 'none';
                    });

                    // Show sections for current page
                    const start = page * sectionsPerPage;
                    const end = start + sectionsPerPage;
                    for (let i = start; i < end && i < sections.length; i++) {
                        sections[i].style.display = 'block';
                    }

                    // Update UI state
                    currentPageSpan.textContent = page + 1;
                    sessionStorage.setItem('currentPage', page);
                    updateButtons();

                    // Smooth scroll to first section
                    window.scrollTo({
                        top: sections[start].offsetTop - 20,
                        behavior: 'smooth'
                    });
                }

                /**
                 * Handle AJAX form submission
                 */
                function handleFormSubmission(form, e) {
                    e.preventDefault();
                    const scrollPos = window.scrollY;
                    const formData = new FormData(form);
                    const saveBtn = form.querySelector('button[type="submit"]');
                    const originalText = saveBtn.innerHTML;

                    // Show loading state
                    saveBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                    saveBtn.disabled = true;

                    // Add CSRF token to headers
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    };

                    fetch(form.action, {
                            method: 'POST',
                            headers: headers,
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    toast: true,
                                    icon: 'success',
                                    title: data.message || 'Saved successfully',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            } else {
                                throw new Error(data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                toast: true,
                                icon: 'error',
                                title: error.message || 'An unexpected error occurred',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        })
                        .finally(() => {
                            // Restore original state
                            window.scrollTo(0, scrollPos);
                            saveBtn.innerHTML = originalText;
                            saveBtn.disabled = false;
                            updateButtons();
                        });
                }

                // Event Listeners
                prevBtn?.addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        showPage(currentPage);
                    }
                });

                nextBtn?.addEventListener('click', function() {
                    if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                        currentPage++;
                        showPage(currentPage);
                    }
                });

                // Field validation on input
                document.querySelectorAll('input[type="number"][name*="EmpScore"], textarea[name="employeeComment"]')
                    .forEach(input => {
                        input.addEventListener('input', function() {
                            validateField(this);
                            updateButtons();
                        });
                    });

                // AJAX form submission handling
                document.querySelectorAll('form.ajax-eval-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        handleFormSubmission(form, e);
                    });
                });

                // Initialize the view
                if (sections.length > 0) {
                    showPage(currentPage);
                }
            });
        </script>
    @endpush
</x-base-layout>
