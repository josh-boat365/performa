# Employee KPI Form - Technical Implementation Guide

## Quick Reference: Copy-Paste Code Snippets

This document provides ready-to-use code snippets to implement the UX/state tracking system on another page.

---

## 1. HTML Structure Template

### Minimal Page Structure
```blade
<x-base-layout>
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

        /* Unsaved form warning border */
        .border-warning {
            border-color: #ffc107 !important;
            border-width: 2px !important;
        }

        /* Saved button styling */
        .btn-saved {
            pointer-events: none;
        }
    </style>

    <div class="container-fluid px-1">
        <!-- Progress Bar -->
        <div class="progress fixed-top" style="height: 10px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                 role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
            </div>
        </div>

        <!-- Page Content -->
        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Evaluation Form</h4>

                        <!-- Pagination Display (only show when not in locked status) -->
                        @if (!in_array($status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                            <div id="pagination-count" class="text-center mb-3">
                                <span><b>Current Page</b></span>
                                <span class="badge rounded-pill bg-primary" id="current-page">1</span> / 
                                <span><b>Last Page</b></span>
                                <span class="badge rounded-pill bg-dark" id="total-pages">1</span>
                            </div>
                        @endif

                        <!-- Forms Container -->
                        <div id="form-sections">
                            @if (isset($items) && $items->isNotEmpty())
                                @foreach ($items as $index => $item)
                                    <div class="section-tab card border border-success" style="border-radius: 10px;">
                                        <div class="card-body" style="background-color: #1eff000d">
                                            
                                            <!-- Form Structure -->
                                            <form class="ajax-eval-form" method="POST" action="{{ route('save.evaluation') }}">
                                                @csrf
                                                
                                                <!-- Hidden metadata -->
                                                <input type="hidden" name="itemId" value="{{ $item->id }}">
                                                <input type="hidden" name="recordType" value="metric">
                                                
                                                <!-- Score Input (3 cols) -->
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Score</label>
                                                        <input class="form-control mb-3" type="number" 
                                                               name="employeeScore"
                                                               placeholder="Enter Score" 
                                                               value="{{ optional($item->savedScore)->score ?? '' }}"
                                                               @disabled(in_array($status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                               required>
                                                    </div>
                                                    
                                                    <!-- Comment Input (9 cols) -->
                                                    <div class="col-md-9">
                                                        <label class="form-label">Comments</label>
                                                        <textarea class="form-control mb-3" type="text" 
                                                                  name="employeeComment"
                                                                  placeholder="Enter your comments"
                                                                  @disabled(in_array($status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                                  rows="3">{{ optional($item->savedScore)->comment ?? '' }}</textarea>
                                                    </div>
                                                </div>

                                                <!-- Save Button (only show if not locked) -->
                                                @if (!in_array($status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" style="height: fit-content" 
                                                                class="btn btn-success">Save</button>
                                                        <div id="ajax-loader" style="display:none;">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </form>

                                            <!-- Supervisor Scores Display (if applicable) -->
                                            @if (isset($item->supervisorScore))
                                                <span class="mb-2 badge rounded-pill bg-success">
                                                    <strong>Supervisor Score and Comment</strong>
                                                </span>
                                                <div class="d-flex gap-3">
                                                    <div class="col-md-3">
                                                        <input class="form-control mb-3" type="number" readonly
                                                               value="{{ $item->supervisorScore->score ?? '' }}">
                                                    </div>
                                                    <div class="col-md-9">
                                                        <textarea class="form-control mb-3" type="text" readonly
                                                                  rows="3">{{ $item->supervisorScore->comment ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>No items available.</p>
                            @endif
                        </div>

                        <hr class="mt-4">

                        <!-- Pagination Controls (only show when not in locked status) -->
                        @if (!in_array($status, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
                            <div class="float-end">
                                <div class="d-flex gap-3 pagination-controls">
                                    <button id="prev-btn" class="btn btn-dark" disabled>Previous</button>
                                    <button id="next-btn" class="btn btn-primary">Next</button>
                                    <button id="submit-btn" type="button" data-bs-toggle="modal"
                                            class="btn btn-success" data-bs-target=".bs-submit-modal-lg" disabled>
                                        Submit for Review
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmation Modal -->
                            <div class="modal fade bs-submit-modal-lg" tabindex="-1" role="dialog"
                                 aria-labelledby="submitModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="submitModalLabel">Confirm Submission</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h4 class="text-center mb-4">
                                                Are you sure you want to <b>Submit</b> this evaluation for review?
                                            </h4>
                                            <form action="{{ route('submit.evaluation') }}" method="POST" id="submitForm">
                                                @csrf
                                                <input type="hidden" name="recordId" value="{{ $recordId }}">
                                                <input type="hidden" name="status" value="REVIEW">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-success">Confirm Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- JavaScript state management will go here -->
    @endpush

</x-base-layout>
```

---

## 2. Complete JavaScript State Management

### Main Script Block (Copy entire script into @push('scripts'))

```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // CONFIGURATION & DOM ELEMENTS
    // ============================================================
    
    const sections = document.querySelectorAll('.section-tab');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const currentPageSpan = document.getElementById('current-page');
    const totalPagesSpan = document.getElementById('total-pages');
    const progressBar = document.getElementById('progress-bar');

    // Session storage keys
    const currentRecordId = '{{ $recordId }}'; // Change entity ID as needed
    const pageStorageKey = `currentPage_record_${currentRecordId}`;
    const lastRecordKey = 'lastViewedRecordId';

    // Pagination settings
    const sectionsPerPage = 3;
    let currentPage = 0;

    // ============================================================
    // INITIALIZATION: Restore page state or reset for new record
    // ============================================================
    
    const lastViewedRecord = sessionStorage.getItem(lastRecordKey);
    
    if (lastViewedRecord === currentRecordId) {
        // Same record - restore page position
        currentPage = parseInt(sessionStorage.getItem(pageStorageKey) || 0);
    } else {
        // Different record - reset to first page
        currentPage = 0;
        sessionStorage.setItem(lastRecordKey, currentRecordId);
        sessionStorage.setItem(pageStorageKey, '0');
    }

    const totalPages = Math.ceil(sections.length / sectionsPerPage);
    totalPagesSpan.textContent = totalPages;

    // ============================================================
    // VALIDATION FUNCTIONS
    // ============================================================

    /**
     * Validate single field
     * Adds/removes is-valid/is-invalid classes
     * Updates parent section border
     */
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

    /**
     * Initialize saved state for all forms on page load
     * Checks if form has pre-filled values
     */
    function initializeSavedState() {
        document.querySelectorAll('form.ajax-eval-form').forEach(form => {
            const scoreInput = form.querySelector('input[type="number"][name="employeeScore"]');
            const saveBtn = form.querySelector('button[type="submit"]');

            if (scoreInput && scoreInput.value.trim() !== '') {
                // Form has pre-filled value (already saved on server)
                form.dataset.saved = 'true';
                if (saveBtn) {
                    saveBtn.textContent = 'Saved';
                    saveBtn.classList.remove('btn-success');
                    saveBtn.classList.add('btn-secondary');
                    saveBtn.disabled = true;
                }
            } else {
                // Form is empty
                form.dataset.saved = 'false';
            }
        });
    }

    /**
     * Mark form as unsaved when user makes changes
     * Updates button appearance and state
     */
    function markFormUnsaved(form) {
        form.dataset.saved = 'false';
        const saveBtn = form.querySelector('button[type="submit"]');
        if (saveBtn) {
            saveBtn.textContent = 'Save';
            saveBtn.classList.remove('btn-secondary');
            saveBtn.classList.add('btn-success');
            saveBtn.disabled = false;
        }
    }

    /**
     * Check if all forms on a specific page are filled and saved
     * Returns: true if page is valid, false otherwise
     */
    function checkInputs(page) {
        const start = page * sectionsPerPage;
        const end = start + sectionsPerPage;
        let allFilled = true;
        let allSaved = true;

        for (let i = start; i < end && i < sections.length; i++) {
            const scoreInputs = sections[i].querySelectorAll('input[type="number"][name="employeeScore"]');
            const form = sections[i].querySelector('form.ajax-eval-form');

            // Check if all score inputs have values
            const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');

            // Check if form is saved
            const isSaved = form ? form.dataset.saved === 'true' : true;

            if (!scoresFilled) {
                allFilled = false;
                sections[i].classList.add('border-danger');
                sections[i].classList.remove('border-warning');
            } else if (!isSaved) {
                allSaved = false;
                sections[i].classList.remove('border-danger');
                sections[i].classList.add('border-warning');
            } else {
                sections[i].classList.remove('border-danger');
                sections[i].classList.remove('border-warning');
            }
        }

        return allFilled && allSaved;
    }

    /**
     * Update progress bar based on all completed sections
     */
    function updateProgressBar() {
        let totalValid = 0;
        sections.forEach(section => {
            const scoreInputs = section.querySelectorAll('input[type="number"][name="employeeScore"]');
            const form = section.querySelector('form.ajax-eval-form');

            const scoresFilled = Array.from(scoreInputs).every(input => input.value.trim() !== '');
            const isSaved = form ? form.dataset.saved === 'true' : true;

            if (scoresFilled && isSaved) {
                totalValid++;
            }
        });

        const percent = Math.round((totalValid / sections.length) * 100);
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        progressBar.textContent = percent + '%';
    }

    /**
     * Update button disabled states based on current page validation
     */
    function updateButtons() {
        prevBtn.disabled = currentPage === 0;
        nextBtn.disabled = currentPage === totalPages - 1 || !checkInputs(currentPage);
        submitBtn.disabled = !Array.from({length: totalPages}).every((_, i) => checkInputs(i));
        updateProgressBar();
    }

    /**
     * Display a specific page of sections
     * Hide all others, show only the page range
     */
    function showPage(page) {
        sections.forEach(section => {
            section.style.display = 'none';
        });

        const start = page * sectionsPerPage;
        const end = start + sectionsPerPage;
        for (let i = start; i < end && i < sections.length; i++) {
            sections[i].style.display = 'block';
        }

        currentPageSpan.textContent = page + 1;
        sessionStorage.setItem(pageStorageKey, page);
        updateButtons();
        
        // Smooth scroll to top of visible section
        if (sections[start]) {
            window.scrollTo({
                top: sections[start].offsetTop,
                behavior: 'smooth'
            });
        }
    }

    // ============================================================
    // EVENT LISTENERS - NAVIGATION BUTTONS
    // ============================================================

    prevBtn.addEventListener('click', function() {
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentPage < totalPages - 1 && checkInputs(currentPage)) {
            currentPage++;
            showPage(currentPage);
        }
    });

    // ============================================================
    // EVENT LISTENERS - FORM INPUTS
    // ============================================================

    document.querySelectorAll('input[type="number"][name="employeeScore"], textarea[name="employeeComment"]')
        .forEach(input => {
            input.addEventListener('input', function() {
                validateField(this);
                
                // Mark form as unsaved when any input changes
                const form = this.closest('form.ajax-eval-form');
                if (form) {
                    markFormUnsaved(form);
                }
                
                updateButtons();
            });
        });

    // ============================================================
    // EVENT LISTENERS - AJAX FORM SUBMISSION
    // ============================================================

    document.querySelectorAll('form.ajax-eval-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const scrollPos = window.scrollY;
            const formData = new FormData(form);
            const saveBtn = form.querySelector('button[type="submit"]');

            // Save state before submission
            sessionStorage.setItem('preserveScrollPosition', scrollPos.toString());
            sessionStorage.setItem(pageStorageKey, currentPage.toString());

            // Show loading state
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
            saveBtn.disabled = true;

            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: formData
            })
            .then(response => {
                // Check for session expiration (401)
                if (response.status === 401) {
                    return response.json().then(data => {
                        if (data.session_expired) {
                            alert('Your session has expired. Please log in again.');
                            window.location.href = data.redirect || '{{ route("login") }}';
                            return null;
                        }
                        return data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // Session expired

                // Store response for post-reload display
                if (data.success) {
                    sessionStorage.setItem('showSuccessToast', JSON.stringify({
                        message: data.message || 'Saved successfully'
                    }));
                } else {
                    sessionStorage.setItem('showErrorToast', JSON.stringify({
                        message: data.message || 'An error occurred'
                    }));
                }

                // Reload page to get fresh data from server
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                sessionStorage.setItem('showErrorToast', JSON.stringify({
                    message: 'An unexpected error occurred'
                }));
                window.location.reload();
            });
        });
    });

    // ============================================================
    // INITIALIZATION & POST-LOAD STATE RESTORATION
    // ============================================================

    // Initialize form states based on pre-filled values
    initializeSavedState();

    // Display initial page
    showPage(currentPage);

    // Restore user's scroll position and show toast messages after page reload
    setTimeout(() => {
        // Restore scroll position
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

        // Show success toast
        const successToast = sessionStorage.getItem('showSuccessToast');
        if (successToast) {
            const toastData = JSON.parse(successToast);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: toastData.message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
            sessionStorage.removeItem('showSuccessToast');
        }

        // Show error toast
        const errorToast = sessionStorage.getItem('showErrorToast');
        if (errorToast) {
            const toastData = JSON.parse(errorToast);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: toastData.message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
            sessionStorage.removeItem('showErrorToast');
        }
    }, 100);
});
</script>

<!-- Second script for next button validation dialogs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('next-btn');

    /**
     * Validate current page before allowing navigation
     * Shows SweetAlert if validation fails
     */
    function validatePage(currentPage) {
        const sections = document.querySelectorAll('.section-tab');
        const sectionsPerPage = 3;
        const start = currentPage * sectionsPerPage;
        const end = start + sectionsPerPage;

        let allSaved = true;
        let emptyField = null;

        for (let i = start; i < end && i < sections.length; i++) {
            const scoreInputs = sections[i].querySelectorAll('input[type="number"][name="employeeScore"]');
            const saveButtons = sections[i].querySelectorAll('button[type="submit"]');

            // Check score fields
            scoreInputs.forEach(input => {
                if (!input.value.trim()) {
                    emptyField = input;
                    input.classList.add('is-invalid');
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Check save button states
            saveButtons.forEach(button => {
                if (!button.classList.contains('btn-secondary')) {
                    allSaved = false;
                }
            });
        }

        if (emptyField) {
            Swal.fire({
                icon: 'error',
                title: 'Incomplete Form',
                text: 'Please fill in all the required score fields before proceeding.'
            });
            return false;
        }

        if (!allSaved) {
            Swal.fire({
                icon: 'warning',
                title: 'Unsaved Changes',
                text: 'Please save all changes before proceeding to the next page.'
            });
            return false;
        }

        return true;
    }

    nextBtn.addEventListener('click', function(event) {
        const currentPage = parseInt(document.getElementById('current-page').textContent) - 1;
        if (!validatePage(currentPage)) {
            event.preventDefault();
        }
    });

    /**
     * Update next button state based on current page validity
     */
    function updateNextButtonState() {
        const currentPage = parseInt(document.getElementById('current-page').textContent) - 1;
        const sections = document.querySelectorAll('.section-tab');
        const sectionsPerPage = 3;
        const start = currentPage * sectionsPerPage;
        const end = start + sectionsPerPage;

        let allSaved = true;
        let allFilled = true;

        for (let i = start; i < end && i < sections.length; i++) {
            const scoreInputs = sections[i].querySelectorAll('input[type="number"][name="employeeScore"]');
            const saveButtons = sections[i].querySelectorAll('button[type="submit"]');

            scoreInputs.forEach(input => {
                if (!input.value.trim()) {
                    allFilled = false;
                }
            });

            saveButtons.forEach(button => {
                if (!button.classList.contains('btn-secondary')) {
                    allSaved = false;
                }
            });
        }

        nextBtn.disabled = !(allFilled && allSaved);
    }

    document.querySelectorAll('input[type="number"][name="employeeScore"], button[type="submit"]')
        .forEach(element => {
            element.addEventListener('input', updateNextButtonState);
            element.addEventListener('click', updateNextButtonState);
        });

    updateNextButtonState();
});
</script>
```

---

## 3. Backend Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    /**
     * Save individual evaluation score
     * Called via AJAX POST from form submission
     */
    public function save(Request request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'recordId' => 'required|integer',
                'itemId' => 'required|integer',
                'employeeScore' => 'required|numeric|min:0|max:100',
                'employeeComment' => 'nullable|string|max:1000',
                'recordType' => 'required|string|in:metric,section'
            ]);

            // Save to database
            $evaluation = Evaluation::updateOrCreate(
                [
                    'record_id' => $validated['recordId'],
                    'item_id' => $validated['itemId'],
                ],
                [
                    'employee_score' => $validated['employeeScore'],
                    'employee_comment' => $validated['employeeComment'],
                    'saved_at' => now(),
                    'saved_by' => auth()->id(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Evaluation saved successfully',
                'data' => $evaluation
            ]);

        } catch (\Throwable $e) {
            \Log::error('Evaluation save error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save evaluation: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Submit evaluation for review
     * Changes status to REVIEW and triggers workflow
     */
    public function submit(Request $request)
    {
        try {
            $validated = $request->validate([
                'recordId' => 'required|integer',
                'status' => 'required|string|in:REVIEW,CONFIRMATION,COMPLETED,PROBLEM'
            ]);

            $evaluation = Evaluation::findOrFail($validated['recordId']);

            // Verify all fields are saved before allowing submission
            $hasMissingScores = $evaluation->evaluationItems()
                ->whereNull('employee_score')
                ->exists();

            if ($hasMissingScores) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot submit: Some required scores are missing'
                ], 422);
            }

            // Update status
            $evaluation->update([
                'status' => $validated['status'],
                'status_updated_at' => now(),
                'status_updated_by' => auth()->id()
            ]);

            // Send notification to supervisor if status = REVIEW
            if ($validated['status'] === 'REVIEW') {
                // Send notification logic
            }

            return response()->json([
                'success' => true,
                'message' => 'Evaluation submitted for review',
                'redirect' => route('evaluations.show', $evaluation->id)
            ]);

        } catch (\Throwable $e) {
            \Log::error('Evaluation submit error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit evaluation: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Load evaluation detail page
     * Pre-fetches all evaluation data with related scores
     */
    public function show($id)
    {
        $evaluation = Evaluation::with([
            'evaluationItems.savedScores',
            'evaluationItems.supervisorScores'
        ])->findOrFail($id);

        return view('evaluation.form', [
            'items' => $evaluation->evaluationItems,
            'recordId' => $evaluation->id,
            'status' => $evaluation->status,
            'currentScore' => $evaluation->current_score,
            'supervisorScore' => $evaluation->supervisor_score
        ]);
    }
}
```

---

## 4. Migration Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Evaluation records table
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('record_type_id'); // KPI ID, batch ID, etc.
            $table->string('record_type'); // 'kpi', 'metric', etc.
            $table->enum('status', ['PENDING', 'REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM'])->default('PENDING');
            
            $table->decimal('employee_total_score', 5, 2)->nullable();
            $table->string('employee_grade')->nullable();
            
            $table->decimal('supervisor_total_score', 5, 2)->nullable();
            $table->string('supervisor_grade')->nullable();
            
            $table->unsignedBigInteger('status_updated_by')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Individual evaluation item scores
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('item_id'); // Metric ID, section ID, etc.
            $table->string('item_type'); // 'metric', 'section'
            
            // Employee submission
            $table->decimal('employee_score', 5, 2)->nullable();
            $table->text('employee_comment')->nullable();
            $table->unsignedBigInteger('employee_saved_by')->nullable();
            $table->timestamp('employee_saved_at')->nullable();
            
            // Supervisor review
            $table->decimal('supervisor_score', 5, 2)->nullable();
            $table->text('supervisor_comment')->nullable();
            $table->unsignedBigInteger('supervisor_saved_by')->nullable();
            $table->timestamp('supervisor_saved_at')->nullable();
            
            // Probing (if applicable)
            $table->decimal('probe_score', 5, 2)->nullable();
            $table->text('probe_comment')->nullable();
            $table->unsignedBigInteger('probe_saved_by')->nullable();
            $table->timestamp('probe_saved_at')->nullable();
            
            $table->timestamps();

            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_items');
        Schema::dropIfExists('evaluations');
    }
};
```

---

## 5. Key Variable Substitutions

When implementing on another page, replace these placeholders:

| Original | Replace With | Purpose |
|----------|------------|---------|
| `$employeeId` | `$recordId` | Your entity ID for session tracking |
| `$kpiStatus` | `$status` | Workflow status field |
| `$appraisal` | `$items` | Collection of evaluable items |
| `section` | `item` | Your individual item entity |
| `sectionEmpScore` | `savedScore` | Your saved score entity |
| `sectionSupScore` | `supervisorScore` | Your supervisor score entity |
| `.section-tab` | `.section-tab` | Keep this class name |
| `.ajax-eval-form` | `.ajax-eval-form` | Keep this class name |
| `employeeScore` | `employeeScore` | Keep score input name |
| `employeeComment` | `employeeComment` | Keep comment textarea name |
| `route('save.appraisal')` | `route('evaluation.save')` | Your save route |
| `route('submit.appraisal')` | `route('evaluation.submit')` | Your submit route |

---

## 6. Routes Configuration

```php
// routes/web.php

Route::middleware(['auth'])->group(function () {
    // Display evaluation form
    Route::get('/evaluation/{id}', [EvaluationController::class, 'show'])->name('evaluation.show');
    
    // Save individual evaluation item
    Route::post('/evaluation/save', [EvaluationController::class, 'save'])->name('evaluation.save');
    
    // Submit evaluation for review
    Route::post('/evaluation/submit', [EvaluationController::class, 'submit'])->name('evaluation.submit');
});
```

---

## 7. Common Implementation Checklist

- [ ] Update all route names to match your routes
- [ ] Replace entity IDs and variable names
- [ ] Create database migrations for storing scores
- [ ] Implement backend controller methods (save, submit)
- [ ] Add validation rules in controller
- [ ] Update form input names: `employeeScore`, `employeeComment`
- [ ] Keep HTML classes: `.section-tab`, `.ajax-eval-form`
- [ ] Keep button IDs: `#prev-btn`, `#next-btn`, `#submit-btn`
- [ ] Keep field IDs: `#current-page`, `#total-pages`, `#progress-bar`
- [ ] Include SweetAlert2 library in layout
- [ ] Include Bootstrap library for modals
- [ ] Test pagination with different section counts
- [ ] Test form save/unsave workflow
- [ ] Test employee switching
- [ ] Test scroll position restoration

---

## 8. Dependencies Required

### JavaScript Libraries
```blade
<!-- In your layout blade template -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
```

### CSS
```blade
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
```

### PHP/Laravel
- Laravel 8.x or higher
- Authentication middleware
- CSRF middleware
- Database with proper migrations

---

## 9. Testing Checklist

### Functionality Tests
- [ ] All form fields validate correctly
- [ ] Save button changes state appropriately
- [ ] Next button disables when page invalid
- [ ] Progress bar updates on save
- [ ] Page position persists for same record
- [ ] Page resets when viewing different record
- [ ] Scroll position restored after save
- [ ] Toast notifications display
- [ ] Modal confirmations work
- [ ] Submit disables until all pages valid

### Edge Cases
- [ ] Single section (totalPages = 1)
- [ ] Large number of sections (10+)
- [ ] Form with no optional fields
- [ ] Rapid multiple saves
- [ ] Session expiration (401 response)
- [ ] Network errors
- [ ] Page refresh during save

### Browser Compatibility
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browsers

---

## 10. Debugging Tips

### Check Session Storage
```javascript
// In browser console
sessionStorage.getItem('currentPage_record_1')
sessionStorage.getItem('lastViewedRecordId')
```

### Check Form State
```javascript
// In browser console
document.querySelector('form.ajax-eval-form').dataset.saved
```

### Monitor AJAX Requests
```javascript
// Check Network tab in DevTools for POST requests to /evaluation/save
// Check response `{success: true/false, message: "..."}`
```

### Progress Bar Debug
```javascript
// In browser console
document.getElementById('progress-bar').textContent
```

