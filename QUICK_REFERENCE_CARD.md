# Quick Reference Card - KPI Form UX/State Tracking

## 🎯 At a Glance

This page tracks form state across multiple pages with sophisticated validation, visual feedback, and session persistence.

---

## 📊 STATE MACHINE AT A GLANCE

```
USER FILLS FORM → SAVES → PAGE RELOADS → USER SEES UPDATED STATE → NEXT PAGE
     ↓              ↓          ↓              ↓
  Unsaved       Saving    Reload w/      Saved
  Yellow        Spinner   Toast Msg      No Border
  Border                  Restored       "Saved" btn
                          Scroll Pos
```

---

## 🎨 Visual Indicators Quick Ref

| What You See | What It Means | What To Do |
|---|---|---|
| **RED border** on section | Scores are empty/incomplete | Fill all score fields |
| **YELLOW border** on section | Scores filled but NOT saved | Click "Save" button |
| **NO border** on section | Scores filled AND saved | Valid - can proceed |
| **Green button** "Save" | Changes ready to save | Click to save |
| **Gray button** "Saved" | Changes already saved | Done - cannot click |
| **Progress bar** 0-100% | % of sections completed | Shows completion |
| **DISABLED Next button** | Current page invalid | Complete current page |
| **ENABLED Next button** | Current page valid | Can advance to next |

---

## 🔄 Form Lifecycle

```
1. LOAD:     Form empty → Yellow border
2. FILL:     User enters score → Yellow border (unsaved)
3. SAVE:     User clicks Save → AJAX post → Page reloads
4. RELOAD:   Server returns updated data → No border (saved)
5. REPEAT:   For each section (3 per page)
6. COMPLETE: All pages valid → Submit button enabled
7. SUBMIT:   User submits for review → Status: REVIEW → Form locked
```

---

## 💾 Session Storage Keys

```javascript
'lastViewedRecordId'           // Which record currently viewing
'currentPage_record_{id}'      // What page user was on
'preserveScrollPosition'       // Y-axis scroll to restore
'showSuccessToast'            // Success message after reload
'showErrorToast'              // Error message after reload
```

---

## 🎮 Button Behaviors Simplified

| Button | Enabled When | Disabled When | Action |
|--------|---|---|---|
| **Prev** | Not on page 1 | On page 1 | Go to previous page |
| **Next** | Page is valid | Page invalid OR on last | Go to next page |
| **Save** | Form unsaved | Form saved | AJAX POST save |
| **Submit** | All pages valid | Any page invalid | Open confirmation |

---

## ✅ Validation Rules

**Field Valid = Has Value**
```
Score input must not be empty → .is-valid or .is-invalid
Comment input optional → No validation required
```

**Page Valid = All Sections Valid**
```
All score inputs filled? ✓
All forms saved? ✓
→ Page can advance to next
```

**Submit Valid = All Pages Valid**
```
Every page on form valid? ✓
→ Submit button enabled
```

---

## 🌊 AJAX Save Flow

```
1. Form Submit
   ├─ Save scroll position
   ├─ Save current page
   ├─ Show spinner + "Saving..."
   └─ Disable button

2. POST /save
   ├─ Send FormData (score, comment, IDs)
   ├─ Include CSRF token
   └─ Server saves to DB

3. Response
   ├─ Success: Store toast data in session
   ├─ Error: Store error toast in session
   └─ Always: Reload page

4. Page Reload
   ├─ 100ms timeout fires
   ├─ Restore scroll position
   ├─ Show toast notification
   ├─ Re-initialize form states
   └─ User continues editing
```

---

## 🎯 Status States & What User Sees

| Status | User Sees | Can Edit? | Visible Controls |
|--------|---|---|---|
| **PENDING** | Empty/partial form | ✓ Yes | Pagination, Save, Submit buttons |
| **REVIEW** | Readonly form | ✗ No | Nothing editable |
| **CONFIRMATION** | Employee scores + Supervisor scores | ✗ No | Accept/Push/Probe buttons |
| **COMPLETED** | All scores readonly | ✗ No | Display only |
| **PROBLEM** | All scores readonly | ✗ No | Display only |

---

## 🚫 Error Scenarios & Recovery

| Error | Symptom | Recovery |
|---|---|---|
| **Session expired** | 401 response | Auto-redirect to login |
| **Validation fails** | 422 response | Reload page, show error toast |
| **Network error** | Fetch error | Reload page, show error toast |
| **Empty required field** | Cannot advance | Red border, cannot click Next |
| **Unsaved changes** | Cannot advance | Yellow border, click Save first |

---

## 📱 Key HTML Elements

```html
<!-- Sections wrapper -->
<div class="section-tab">
  <!-- Form -->
  <form class="ajax-eval-form" action="{{ route(...) }}" method="POST">
    <!-- Score (REQUIRED) -->
    <input type="number" name="employeeScore" required>
    
    <!-- Comment (OPTIONAL) -->
    <textarea name="employeeComment"></textarea>
    
    <!-- Submit button -->
    <button type="submit" class="btn btn-success">Save</button>
  </form>
</div>

<!-- Navigation buttons -->
<button id="prev-btn" class="btn btn-dark">Previous</button>
<button id="next-btn" class="btn btn-primary">Next</button>
<button id="submit-btn" class="btn btn-success">Submit</button>

<!-- Status displays -->
<span id="current-page">1</span>
<span id="total-pages">4</span>

<!-- Progress -->
<div id="progress-bar" class="progress-bar"></div>
```

---

## 🔧 Required Configuration

```javascript
// CHANGE THESE FOR YOUR PAGE:
const currentRecordId = '{{ $recordId }}';        // Your entity ID
const pageStorageKey = `currentPage_record_${currentRecordId}`;
const sectionsPerPage = 3;                        // Items to show per page

// KEEP THESE THE SAME:
const sections = document.querySelectorAll('.section-tab');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');
const submitBtn = document.getElementById('submit-btn');
```

---

## 📋 Implementation Checklist (Quick)

- [ ] Create table for scores in database
- [ ] Create form with .ajax-eval-form class
- [ ] Add section wrapper with .section-tab class
- [ ] Add score input: `name="employeeScore"`
- [ ] Add comment textarea: `name="employeeComment"`
- [ ] Add buttons with correct IDs: #prev-btn, #next-btn, #submit-btn
- [ ] Add status displays: #current-page, #total-pages, #progress-bar
- [ ] Copy JavaScript (both scripts fully)
- [ ] Copy CSS for borders and buttons
- [ ] Create /save route and controller
- [ ] Create /submit route and controller
- [ ] Include SweetAlert2 library
- [ ] Test: Fill one form, save, see state change
- [ ] Test: Navigate pages, see position restored
- [ ] Test: Fill all, submit, see confirmation

---

## 🧪 Quick Test Scenarios

**Test Unsaved State:**
1. Enter score → See yellow border
2. Click Save → See "Saving..."
3. Wait for reload → See "Saved" button (gray)

**Test Pagination:**
1. Fill 3 sections → Next button enabled
2. Click Next → See page 2 of 4
3. Click Prev → See page 1 of 4 (content preserved)

**Test Switching Records:**
1. View Record A on page 2
2. Navigate to Record B → Page resets to 1
3. Navigate back to Record A → Page 2 restored

**Test Submit:**
1. Fill all 4 pages
2. Submit button enabled
3. Click Submit → Modal confirmation
4. Confirm → Status changes to REVIEW → Form locked

---

## 🐛 Debugging Commands (Browser Console)

```javascript
// Check current page
sessionStorage.getItem('currentPage_record_1')

// Check form states
document.querySelectorAll('form.ajax-eval-form').forEach(f => {
  console.log('Form saved:', f.dataset.saved)
})

// Check progress
console.log(document.getElementById('progress-bar').textContent)

// Check current page display
console.log(
  'Current:', document.getElementById('current-page').textContent,
  'Total:', document.getElementById('total-pages').textContent
)

// Check if Next button should be enabled
console.log('Next disabled:', document.getElementById('next-btn').disabled)
```

---

## 🎨 CSS Classes Applied Dynamically

```css
/* These classes are added/removed by JavaScript */

.border-danger {              /* Red border - scores empty */
  border-color: #dc3545;
  border-width: 2px;
}

.border-warning {             /* Yellow border - unsaved */
  border-color: #ffc107;
  border-width: 2px;
}

.is-valid {                   /* Green input - has value */
  border-color: #198754;      /* Bootstrap green */
}

.is-invalid {                 /* Red input - empty */
  border-color: #dc3545;      /* Bootstrap red */
}

.btn-success {                /* Green button - unsaved */
  /* Existing Bootstrap class */
}

.btn-secondary {              /* Gray button - saved */
  /* Existing Bootstrap class */
}
```

---

## 🔌 Route Examples

```php
// routes/web.php

// Display form page
Route::get('/evaluation/{id}', [EvaluationController::class, 'show'])
  ->name('evaluation.show');

// Save individual score (AJAX)
Route::post('/evaluation/save', [EvaluationController::class, 'save'])
  ->name('evaluation.save');

// Submit for review
Route::post('/evaluation/submit', [EvaluationController::class, 'submit'])
  ->name('evaluation.submit');
```

---

## 📞 Key Functions Reference

| Function | Purpose | Call When |
|----------|---------|-----------|
| `validateField(field)` | Check field has value | Input changes |
| `initializeSavedState()` | Set form state on load | Page loads |
| `markFormUnsaved(form)` | Mark form as unsaved | User types |
| `checkInputs(page)` | Validate page | Before advance |
| `updateProgressBar()` | Update % complete | State changes |
| `updateButtons()` | Enable/disable buttons | State changes |
| `showPage(page)` | Display page's sections | Navigate |

---

## 🎓 Common Issues & Fixes

| Problem | Cause | Fix |
|---------|-------|-----|
| Next button always disabled | `checkInputs()` returning false | Check: scores filled + forms saved |
| Form state doesn't persist | SessionStorage keys wrong | Use: `currentPage_record_${id}` |
| Scroll not restored | `preserveScrollPosition` not cleared | Check: setTimeout clears storage |
| Toast doesn't show | SweetAlert2 not included | Add: `<script src="sweetalert2"></script>` |
| Borders not showing | CSS classes not applied | Check: updateButtons() is called |
| AJAX always fails | CSRF token missing | Add: X-CSRF-TOKEN header |

---

## 📚 Documentation Files

| File | Contains |
|------|----------|
| UX_STATE_TRACKING_DOCUMENTATION.md | Detailed state machine breakdown |
| IMPLEMENTATION_CODE_SNIPPETS.md | Copy-paste code ready to use |
| STATE_DIAGRAMS_AND_FLOWS.md | Visual workflow diagrams |
| README_DOCUMENTATION.md | Overview & getting started guide |
| THIS FILE | Quick reference card |

---

## ⚡ Copy-Paste Blocks

**Minimal JavaScript to include:**
- First script: Main state management (700+ lines)
- Second script: Next button validation (100+ lines)

**Minimal HTML structure:**
- `.section-tab` wrapper
- `.ajax-eval-form` form
- Save button inside form
- Prev/Next/Submit buttons outside forms

**Minimal CSS:**
```css
input[type="number"] { -moz-appearance: textfield; }
.border-warning { border-color: #ffc107 !important; }
.btn-saved { pointer-events: none; }
```

---

## 🚀 30-Second Summary

**What it does:**
- Lets users fill forms on multiple pages
- Saves each page independently via AJAX
- Shows visual feedback (borders, button state)
- Tracks progress with bar
- Persists page position per user/record
- Handles errors gracefully

**How to implement:**
1. Copy HTML structure from guide
2. Copy both JavaScript scripts
3. Create backend routes
4. Database migration
5. Test

**Key features:**
- ✅ Form save state tracking
- ✅ Pagination with persistence
- ✅ Real-time validation
- ✅ Progress tracking
- ✅ AJAX with page reload
- ✅ Session management
- ✅ Error handling

**To share with Claude:**
Share all 4 documentation files + this card = Complete handoff ✓

---

**Created:** 2026-02-23 | **For:** Replication on New Pages | **Status:** Complete ✓
