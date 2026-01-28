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

    <style>
        /* Hide number input spinners/arrows */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        input[type="number"] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }

        /* Progress bar container - sticky at top */
        .progress-container {
            position: sticky;
            top: 60px;
            z-index: 1020;
            background: #fff;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-wrapper .progress {
            flex: 1;
            height: 20px;
            border-radius: 10px;

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // --- Scroll Retention ---
                    const scrollKey = 'supervisorScoreFormScroll_' + '{{ $employeeId }}';
                    if (sessionStorage.getItem(scrollKey)) {
                        window.scrollTo({
                            top: parseInt(sessionStorage.getItem(scrollKey)),
                            behavior: 'auto'
                        });
                    }
                    window.addEventListener('scroll', function () {
                        sessionStorage.setItem(scrollKey, window.scrollY);
                    });

                    // --- Numeric Validation ---
                    document.querySelectorAll('input[type="number"][name*="SupScore"]').forEach(input => {
                        input.setAttribute('required', 'required');
                        input.setAttribute('min', '0');
                        if (!input.hasAttribute('max')) {
                            input.setAttribute('max', '100');
                        }
                        input.addEventListener('input', function () {
                            let val = this.value;
                            if (val !== '' && (isNaN(val) || val < 0 || val > parseInt(this.getAttribute('max')))) {
                                this.classList.add('is-invalid');
                                this.classList.remove('is-valid');
                            } else if (val !== '') {
                                this.classList.remove('is-invalid');
                                this.classList.add('is-valid');
                            } else {
                                this.classList.remove('is-valid');
                                this.classList.add('is-invalid');
                            }
                        });
                    });

                    // --- AJAX Save: Reload after save to update data ---
                    document.querySelectorAll('form.ajax-sup-eval-form, form.section-form').forEach(form => {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            const saveBtn = form.querySelector('button.btn-save');
                            if (!saveBtn) return;
                            const originalHTML = saveBtn.innerHTML;
                            saveBtn.classList.add('btn-saving');
                            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Saving...';
                            saveBtn.disabled = true;

                            const formData = new FormData(form);
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    saveBtn.classList.remove('btn-saving');
                                    saveBtn.disabled = false;
                                    if (data.success) {
                                        saveBtn.classList.add('btn-saved');
                                        saveBtn.innerHTML = '<i class="bx bx-check me-1"></i>Saved';
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                toast: true,
                                                icon: 'success',
                                                title: data.message || 'Saved!',
                                                position: 'top-end',
                                                showConfirmButton: false,
                                                timer: 2000,
                                                timerProgressBar: true
                                            });
                                        }
                                        setTimeout(() => window.location.reload(), 800);
                                    } else {
                                        saveBtn.innerHTML = 'Save';
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                toast: true,
                                                icon: 'error',
                                                title: data.message || 'Save failed',
                                                position: 'top-end',
                                                showConfirmButton: false,
                                                timer: 2000,
                                                timerProgressBar: true
                                            });
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    saveBtn.classList.remove('btn-saving');
                                    saveBtn.disabled = false;
                                    saveBtn.innerHTML = 'Save';
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            icon: 'error',
                                            title: 'An error occurred!',
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        });
                                    }
                                });
                        });
                    });
                });
            </script>
            @endpush
                                                                                                title="Max score: {{ $metric->metricScore }}"
                                                                                                placeholder="Score" required
                                                                                                value="{{ optional($metric->metricEmpScore)->metricSupScore == 0 ? '' : optional($metric->metricEmpScore)->metricSupScore }}">
                                                                                        </div>
                                                                                        <div class="col-md-9">
                                                                                            <textarea class="form-control comment-input"
                                                                                                name="supervisorComment"
                                                                                                @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))
                                                                                                placeholder="Enter your comments"
                                                                                                rows="2">{{ $metric->metricEmpScore->supervisorComment ?? '' }}</textarea>
                                                                                        </div>
                                                                                        <input type="hidden" name="scoreId"
                                                                                            value="{{ $metric->metricEmpScore->id ?? '' }}">
                                                                                        <button type="submit"
                                                                                            class="btn btn-primary btn-save"
                                                                                            style="height: fit-content"
                                                                                            @disabled(isset($metric->metricEmpScore) && in_array($metric->metricEmpScore->status, ['CONFIRMATION', 'PROBLEM']))>
                                                                                            <i class="bx bx-save me-1"></i>Save
                                                                                        </button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
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
                                <div class="text-center py-4">
                                    <i class="bx bx-info-circle text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">No KPIs available for this employee.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer with Pagination and Submit -->
                    @if (!isset($metric->metricEmpScore) || !in_array($metric->metricEmpScore->status ?? '', ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
                        <div class="card-footer bg-white pagination-sticky">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <button id="prev-btn" class="btn btn-dark" disabled>
                                        <i class="bx bx-chevron-left me-1"></i>Previous
                                    </button>
                                    <button id="next-btn" class="btn btn-primary">
                                        Next<i class="bx bx-chevron-right ms-1"></i>
                                    </button>
                                </div>
                                <button id="submit-btn" type="button" data-bs-toggle="modal" class="btn btn-success"
                                    data-bs-target=".bs-submit-appraisal-modal-lg" disabled>
                                    <i class="bx bx-check-circle me-1"></i>Submit Appraisal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if (!isset($metric->metricEmpScore) || !in_array($metric->metricEmpScore->status ?? '', ['CONFIRMATION', 'PROBLEM', 'COMPLETED']))
        <div class="modal fade bs-submit-appraisal-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="myLargeModalLabel">
                            <i class="bx bx-check-circle me-2"></i>Confirm Appraisal Submit
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4" style="max-height: 60vh; overflow-y: auto;">
                        <i class="bx bx-question-mark text-warning" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Submit employee Appraisal for Confirmation?</h5>
                        <p class="text-muted">This action will send the appraisal to the employee for confirmation.</p>

                        <form action="{{ route('submit.appraisal') }}" method="POST">
                            @csrf
                            <input type="hidden" name="employeeId" value="{{ $employeeId }}">
                            <input type="hidden" name="kpiId" value="{{ $kpiId }}">
                            <input type="hidden" name="batchId" value="{{ $batchId }}">
                            <input type="hidden" name="supervisorId" value="{{ $supervisorId }}">
                            <input type="hidden" name="status" value="CONFIRMATION">

                            <div class="mb-3 text-start">
                                <label for="supervisorRecommendation" class="form-label">
                                    <i class="bx bx-message-detail me-1"></i>Supervisor Recommendation (Optional)
                                </label>
                                <textarea class="form-control" id="supervisorRecommendation" name="supervisorRecommendation"
                                    rows="4" placeholder="Enter your recommendation here..." style="word-break: break-word; white-space: pre-line;"></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="submitReviewButton" class="btn btn-success">
                                    <i class="bx bx-send me-1"></i>Submit Employee Appraisal For Confirmation
                                </button>
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
                const progressText = document.getElementById('progress-text');

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

                if (totalPagesSpan) totalPagesSpan.textContent = totalPages;

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
                        const scoreInputs = sections[i].querySelectorAll('input[type="number"][name*="SupScore"]');

                        const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

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
                        const scoreInputs = section.querySelectorAll('input[type="number"][name*="SupScore"]');
                        const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
                        if (scoresFilled) totalValid++;
                    });
                    const percent = sections.length > 0 ? Math.round((totalValid / sections.length) * 100) : 0;

                    if (progressBar) {
                        progressBar.style.width = percent + '%';
                        progressBar.setAttribute('aria-valuenow', percent);

                        // Color coding based on progress
                        progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
                        if (percent < 50) {
                            progressBar.classList.add('bg-danger');
                        } else if (percent < 100) {
                            progressBar.classList.add('bg-warning');
                        } else {
                            progressBar.classList.add('bg-success');
                        }
                    }
                    if (progressText) {
                        progressText.textContent = percent + '%';
                    }
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
                    sessionStorage.setItem(pageStorageKey, page);
                    updateButtons();
                    window.scrollTo({
                        top: sections[start]?.offsetTop - 150 || 0,
                        behavior: 'smooth'
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        if (currentPage > 0) {
                            currentPage--;
                            showPage(currentPage);
                        }
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
                            currentPage++;
                            showPage(currentPage);
                        }
                    });
                }

                document.querySelectorAll('input[type="number"][name*="SupScore"], textarea[name="supervisorComment"]')
                    .forEach(input => {
                        input.addEventListener('input', function () {
                            validateField(this);
                            updateButtons();
                        });
                    });

                // Modified AJAX form handler with dynamic UI update
                document.querySelectorAll('form.ajax-sup-eval-form, form.section-form').forEach(form => {
                    form.addEventListener('submit', async function (e) {
                        e.preventDefault();
                        const scrollPos = window.scrollY;
                        const formData = new FormData(form);
                        const saveBtn = form.querySelector('button[type="submit"]');
                        const originalHTML = saveBtn.innerHTML;

                        // Store scroll position
                        sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());

                        saveBtn.classList.add('btn-saving');
                        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...';
                        saveBtn.disabled = true;

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.success) {
                                showToast('success', data.message || 'Saved successfully');
                                // Dynamically update the DOM instead of reloading
                                updateFormUI(form, data);
                            } else {
                                showToast('error', data.message || 'An error occurred');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showToast('error', error.message || 'An unexpected error occurred. Please try again.');
                        } finally {
                            saveBtn.classList.remove('btn-saving');
                            saveBtn.innerHTML = originalHTML;
                            saveBtn.disabled = false;
                        }
                    });
                });

                function updateFormUI(form, data) {
                    // Example: Update score and comment fields dynamically
                    const scoreInput = form.querySelector('input[name*="SupScore"]');
                    const commentInput = form.querySelector('textarea[name="supervisorComment"]');

                    if (scoreInput && data.updatedScore) {
                        scoreInput.value = data.updatedScore;
                    }

                    if (commentInput && data.updatedComment) {
                        commentInput.value = data.updatedComment;
                    }

                    // Additional UI updates can be added here
                }

                // Show the initial page
                showPage(currentPage);

                // Check for toast messages after page refresh and restore scroll position
                setTimeout(() => {
                    const savedScrollPos = sessionStorage.getItem('preserveScrollPosition');
                    if (savedScrollPos) {
                        const scrollPos = parseInt(savedScrollPos);
                        if (!isNaN(scrollPos)) {
                            window.scrollTo({
                                top: scrollPos,
                                behavior: 'instant'
                            });
                        }
                        sessionStorage.removeItem('preserveScrollPosition');
                    }

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

</x-base-layout>
