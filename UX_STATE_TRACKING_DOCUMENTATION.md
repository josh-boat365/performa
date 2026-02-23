# Employee KPI Form - UX & State Tracking Documentation

## Table of Contents
1. [Overview](#overview)
2. [Global States](#global-states)
3. [Form States](#form-states)
4. [Pagination & Navigation](#pagination--navigation)
5. [Validation System](#validation-system)
6. [UI/UX Indicators](#uiux-indicators)
7. [Button States & Behaviors](#button-states--behaviors)
8. [AJAX Form Submission](#ajax-form-submission)
9. [Session Management](#session-management)
10. [Progress Tracking](#progress-tracking)
11. [Workflow States](#workflow-states)
12. [Key Implementation Details](#key-implementation-details)

---

## Overview

This page implements a multi-step, multi-form evaluation system with sophisticated state tracking, client-side persistence, and real-time validation. The system manages:
- **Multiple KPI sections** with nested metrics or single scores
- **Paginated form display** (3 sections per page)
- **Form save state tracking** (unsaved/saved)
- **Field validation** with visual feedback
- **Progress tracking** across all forms
- **Workflow status control** based on appraisal lifecycle

---

## Global States

### 1. **Workflow Status State** (Server-side, HTML/Blade)
Controls what UI elements are visible and interactive based on the appraisal's progress.

| Status | Badge Color | Visible UI | User Action | Description |
|--------|------------|------------|------------|------------|
| `PENDING` | `dark` | Form disabled, pagination hidden | View only | Initial state, form not yet submitted |
| `REVIEW` | `warning` | Form readonly, pagination hidden | Cannot edit | Supervisor is reviewing employee's submission |
| `CONFIRMATION` | `primary` | Accept/Push/Probe buttons show | Accept/Reject/Probe | Awaiting employee to confirm or challenge supervisor's scores |
| `COMPLETED` | `success` | Readonly display | View only | Appraisal finalized |
| `PROBLEM` | `danger` | Readonly display with probe scores | View only | Flagged for further review (probing) |

**Implementation:**
```blade
@if (in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
    <!-- Hide pagination and form inputs -->
@else
    <!-- Show pagination and editable form -->
@endif
```

### 2. **Employee ID Session State** (Client-side Session Storage)
Tracks which employee is currently being viewed to prevent page persistence issues when switching between employees.

**Keys:**
- `lastViewedEmployeeId` - Stores the previous employee ID
- `currentPage_employee_{employeeId}` - Stores page position per employee

**Purpose:** Reset pagination to page 0 when switching employees; restore page position for same employee.

---

## Form States

### 1. **Individual Form Save State** (Per Form, Client-side)
Each `form.ajax-eval-form` tracks its own saved/unsaved state.

**State Storage:**
```javascript
form.dataset.saved = 'true' | 'false'
```

**State Transitions:**

```
Initial Load:
├─ If input has pre-filled value → saved = 'true'
└─ If input is empty → saved = 'false'

On Input Change:
└─ Any change to score field → saved = 'false'

After AJAX Save:
└─ Page reloads → reinitialize based on server response
```

### 2. **Form Save Button State**
The submit button reflects the form's save state:

| State | Button Text | Button Class | Disabled | Cursor |
|-------|------------|--------------|----------|--------|
| **Saved** | "Saved" | `btn-secondary` | true | not-allowed |
| **Unsaved** | "Save" | `btn-success` | false | pointer |

**Implementation:**
```javascript
// Saved state
saveBtn.textContent = 'Saved';
saveBtn.classList.remove('btn-success');
saveBtn.classList.add('btn-secondary');
saveBtn.disabled = true;

// Unsaved state
saveBtn.textContent = 'Save';
saveBtn.classList.remove('btn-secondary');
saveBtn.classList.add('btn-success');
saveBtn.disabled = false;
```

---

## Pagination & Navigation

### 1. **Pagination Configuration**
- **Sections per page:** 3
- **Total pages:** `Math.ceil(totalSections / 3)`
- **Page numbering:** 1-based (displayed), 0-based (internal)

### 2. **Page Storage & Restoration**

**Session Storage Keys:**
```javascript
pageStorageKey = `currentPage_employee_${employeeId}`
```

**Restoration Logic:**
```javascript
const lastViewedEmployee = sessionStorage.getItem('lastEmployeeKey');

if (lastViewedEmployee === currentEmployeeId) {
    // Same employee → restore page
    currentPage = parseInt(sessionStorage.getItem(pageStorageKey) || 0);
} else {
    // Different employee → reset to 0
    currentPage = 0;
    sessionStorage.setItem(lastEmployeeKey, currentEmployeeId);
}
```

### 3. **Page Display Logic**
Only sections within the current page range are displayed:
```javascript
const start = currentPage * sectionsPerPage;   // e.g., 0-2 for page 1
const end = start + sectionsPerPage;           

for (let i = start; i < end && i < sections.length; i++) {
    sections[i].style.display = 'block';
}
```

### 4. **Navigation Buttons**

#### **Previous Button**
- **Disabled:** When on first page (currentPage === 0)
- **Enabled:** When on any page other than first
- **Action:** Decrements currentPage and displays previous section range

#### **Next Button**
- **Disabled:** 
  - When on last page (currentPage === totalPages - 1), OR
  - When current page validation fails (!checkInputs(currentPage))
- **Enabled:** When not on last page AND current page is valid
- **Action:** Increments currentPage and displays next section range

#### **Submit Appraisal Button**
- **Disabled:** Until ALL pages pass validation
- **Enabled:** When every page has all fields filled AND saved
- **Action:** Opens confirmation modal
- **Triggers:** Form submission with status = 'REVIEW'

---

## Validation System

### 1. **Field-Level Validation**

**Function:** `validateField(field)`

**Validates:**
- Score inputs (numeric fields with name containing "EmpScore")
- Field value is not empty after trim

**Actions on Invalid:**
```javascript
field.classList.add('is-invalid');
field.closest('.section-tab')?.classList.add('border-danger');
```

**Actions on Valid:**
```javascript
field.classList.remove('is-invalid');
field.classList.add('is-valid');
field.closest('.section-tab')?.classList.remove('border-danger');
```

**Triggers:** 
- On input change event
- On field blur

### 2. **Page-Level Validation**

**Function:** `checkInputs(page)`

**Validates All Sections on Page:**
```
For each section on page:
  ├─ Check: All score inputs filled
  ├─ Check: Form saved state === 'true'
  └─ If failed:
     ├─ Add 'border-danger' (red) if scores empty
     └─ Add 'border-warning' (yellow) if not saved
```

**Returns:** `true` only if ALL sections are filled AND saved

**Visual Feedback:**
| Condition | Border Color | Border Width |
|-----------|------------|--------------|
| Empty scores | Red (`border-danger`) | 2px |
| Unsaved changes | Yellow (`border-warning`) | 2px |
| Valid & Saved | None | 0px |

### 3. **Form-Wide Validation**

**Function:** `checkInputs()` - Validates entire form across all pages

**Condition:** All pages must pass their individual validation:
```javascript
!Array.from({length: totalPages}).every((_, i) => checkInputs(i))
```

---

## UI/UX Indicators

### 1. **Section Border Indicators**

**CSS Classes Applied to `.section-tab`:**

```css
/* Dangerous state - empty required fields */
.border-danger {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

/* Warning state - unsaved changes */
.border-warning {
    border-color: #ffc107 !important;
    border-width: 2px !important;
}
```

**Hierarchy:** Danger (red) takes precedence over warning (yellow)
- If scores empty → show red border
- Else if not saved → show yellow border
- Else → no border

### 2. **Input Field Indicators**

**Bootstrap Classes:**
- `.is-invalid` - Red border around input (scores empty)
- `.is-valid` - Green border around input (scores filled)

### 3. **Progress Bar** (Fixed Top)
- **Location:** Fixed to top of page, 10px height
- **Updates:** Continuously as forms are saved
- **Calculation:** 
  ```
  percentage = (number of saved & filled sections / total sections) * 100
  ```
- **Attributes:**
  - `aria-valuenow` - Current percentage
  - `aria-valuemin` - 0
  - `aria-valuemax` - 100
  - Text content - Percentage display

### 4. **Status Badge**
Located in "Appraisal Grades Summary" section

**Colors:**
- `PENDING` → Dark gray
- `REVIEW` → Orange/Warning
- `CONFIRMATION` → Blue/Primary
- `COMPLETED` → Green/Success
- `PROBLEM` → Red/Danger

---

## Button States & Behaviors

### 1. **Previous Button** (`#prev-btn`)
```
Initial: disabled = (currentPage === 0)

Click Event:
  if (currentPage > 0) {
    currentPage--
    showPage(currentPage)
  }

Updated on:
  - Page change
  - Input change
  - Form save
  - Page load
```

### 2. **Next Button** (`#next-btn`)
```
Initial: disabled = (currentPage === totalPages - 1 || !checkInputs(currentPage))

Click Event (with validation):
  validatePage(currentPage) {
    - Check all fields filled
    - Check all forms saved
    - If invalid: show Swal alert
    - Return: true/false
  }
  
  if (validatePage) {
    currentPage++
    showPage(currentPage)
  }

Updated on:
  - Page change
  - Input change
  - Form save
  - Button click
  - Save button state change
```

### 3. **Save Button (Per Form)** 
**Selectors:** `button[type="submit"]` within `form.ajax-eval-form`

```
Initial State Determination:
  if (input has pre-filled value)
    state = 'saved'
  else
    state = 'unsaved'

Transitions:
  saved → unsaved:
    On input change
    Button text: "Saved" → "Save"
    Button class: btn-secondary → btn-success
    Button disabled: true → false
    
  unsaved → saved:
    After AJAX POST success
    Page reloads → reinitialize based on server data

Click/Submit Handler:
  1. Prevent default form submission
  2. Show loading spinner with "Saving..." text
  3. Send AJAX POST with FormData
  4. On success: Reload page
  5. On error: Reload page (forces refresh)
```

### 4. **Submit Appraisal Button** (`#submit-btn`)
```
Initial: disabled = !(all pages pass checkInputs)

Enabled When:
  - Every page has all fields filled
  - Every page has all forms saved

Click Handler:
  Opens confirmation modal: data-bs-target=".bs-delete-modal-lg"
  
Modal Actions:
  Confirm → Submit form with:
    - Method: POST
    - Route: route('submit.appraisal')
    - Hidden inputs:
      * employeeId
      * kpiId
      * batchId
      * status = 'REVIEW'
    - CSRF token included
```

### 5. **Confirmation Actions** (When status === 'CONFIRMATION')

**Accept Button:**
- Opens modal: `.bs-delete-modal-lg`
- Modal asks: "Are you sure you want to Accept this scores from your Supervisor?"
- Submit sets status = 'COMPLETED'

**Push for Review Button:**
- Opens modal: `.bs-push-review-modal-lg`
- Modal asks: "Are you sure you want to Submit your Appraisal back to your Supervisor for Review?"
- Submit sets status = 'REVIEW'

**Probe Button:**
- Direct link to: `route('show.employee.probe', $kpi->kpi->kpiId)`
- No modal confirmation

---

## AJAX Form Submission

### 1. **Form Selection**
```javascript
document.querySelectorAll('form.ajax-eval-form')
```

### 2. **Submission Handler**

**Event:** `submit` on form.ajax-eval-form

**Steps:**

1. **Prevent Default**
   ```javascript
   e.preventDefault()
   ```

2. **Capture State Before Submission**
   ```javascript
   const scrollPos = window.scrollY
   sessionStorage.setItem('preserveScrollPosition', scrollPos)
   sessionStorage.setItem(pageStorageKey, currentPage)
   ```

3. **Show Loading Indicator**
   ```javascript
   saveBtn.innerHTML = '<span class="spinner-border...">Saving...</span>'
   saveBtn.disabled = true
   ```

4. **Prepare Request**
   ```javascript
   fetch(form.action, {
     method: 'POST',
     headers: {
       'X-Requested-With': 'XMLHttpRequest',
       'X-CSRF-TOKEN': tokenFromMeta
     },
     body: formData
   })
   ```

5. **Handle Response**
   - **401 Status:** Session expired → Redirect to login
   - **Success Response:** Store toast data in sessionStorage
   - **Error Response:** Store error toast data
   - **Finally:** Reload page (`window.location.reload()`)

### 3. **Response Handling**

**Success Response Structure:**
```json
{
  "success": true,
  "message": "Saved successfully"
}
```

**Stored in Session Before Reload:**
```javascript
sessionStorage.setItem('showSuccessToast', JSON.stringify({
  message: data.message || 'Saved successfully'
}))
```

### 4. **Session Expiration Handling**

```javascript
if (response.status === 401) {
  return response.json().then(data => {
    if (data.session_expired) {
      alert('Your session has expired. Please log in again.')
      window.location.href = data.redirect || route('login')
      return null
    }
    return data
  })
}
```

---

## Session Management

### 1. **Session Storage Keys**

| Key | Type | Purpose | Lifecycle |
|-----|------|---------|-----------|
| `lastViewedEmployeeId` | String | Tracks current employee context | Persists across page reloads |
| `currentPage_employee_{employeeId}` | String | Stores page position per employee | Persists across page reloads |
| `preserveScrollPosition` | String | Stores Y-axis scroll position | Cleared after page reload |
| `showSuccessToast` | JSON String | Success message for post-reload display | Cleared after toast shown |
| `showErrorToast` | JSON String | Error message for post-reload display | Cleared after toast shown |

### 2. **Toast Message Display After Reload**

**Timing:** 100ms after page load

**Success Toast:**
```javascript
if (sessionStorage.getItem('showSuccessToast')) {
  Swal.fire({
    toast: true,
    icon: 'success',
    title: message,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  })
}
```

**Error Toast:** Same structure with `icon: 'error'`

---

## Progress Tracking

### 1. **Progress Bar Calculation**

**Function:** `updateProgressBar()`

```javascript
let totalValid = 0

sections.forEach(section => {
  const scoresFilled = // All score inputs have values
  const isSaved = // form.dataset.saved === 'true'
  
  if (scoresFilled && isSaved) {
    totalValid++
  }
})

const percent = Math.round((totalValid / sections.length) * 100)
progressBar.style.width = percent + '%'
progressBar.textContent = percent + '%'
```

**Criteria for "Valid" Section:**
1. All score inputs are filled (non-empty after trim)
2. Form is marked as saved (`form.dataset.saved === 'true'`)

**Updates Triggered By:**
- Input change
- Form submission
- Button state change
- Page navigation

### 2. **Progress Bar DOM Updates**

```javascript
progressBar.setAttribute('aria-valuenow', percent)
progressBar.setAttribute('aria-valuemin', '0')
progressBar.setAttribute('aria-valuemax', '100')
```

---

## Workflow States

### 1. **State Diagram**

```
PENDING
  ├─ Employee fills form
  └─ Clicks "Submit Appraisal"
     └─ Status → REVIEW
         ├─ Form becomes readonly
         ├─ Pagination hidden
         └─ Supervisor reviews & scores
            └─ Status → CONFIRMATION
                ├─ Employee sees Supervisor's scores
                ├─ Employee can:
                │  ├─ Accept → Status → COMPLETED
                │  ├─ Push for Review → Status → REVIEW
                │  └─ Probe → Status → PROBLEM
                └─ COMPLETED
                   └─ Appraisal finalized
```

### 2. **Conditional UI Rendering by Status**

**PENDING State:**
```blade
@if (!in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
    <!-- Show pagination, edit buttons, submit button -->
@endif
```

**REVIEW/CONFIRMATION/COMPLETED/PROBLEM States:**
```blade
@if (in_array($kpiStatus, ['REVIEW', 'CONFIRMATION', 'COMPLETED', 'PROBLEM']))
    <!-- Hide form inputs, show readonly display -->
    <!-- Show modals and action buttons only if CONFIRMATION -->
@endif
```

---

## Key Implementation Details

### 1. **Event Listeners**

| Element | Event | Handler | Effect |
|---------|-------|---------|--------|
| Score inputs, textareas | `input` | `validateField()`, `markFormUnsaved()`, `updateButtons()` | Real-time validation & state sync |
| Forms (ajax-eval-form) | `submit` | AJAX submission with page reload | Data persistence |
| Previous button | `click` | Decrement page, show page | Navigation |
| Next button | `click` | Validate page, increment, show page | Navigation with validation |
| Submit button | `click` | Open confirmation modal | Workflow advancement |

### 2. **Data Attributes**

**Form Saved State:**
```html
<form class="ajax-eval-form" data-saved="true|false" action="{{ route(...) }}" method="POST">
```

### 3. **CSS Classes for State Indication**

```css
.is-valid { /* Green border */ }
.is-invalid { /* Red border */ }
.border-danger { /* Red section border */ }
.border-warning { /* Yellow section border */ }
.btn-success { /* Green button (active/unsaved) */ }
.btn-secondary { /* Gray button (saved/disabled) */ }
```

### 4. **Form Data Structure**

Each form typically contains:
```html
<form class="ajax-eval-form" method="POST" action="{{ route('save.evaluation') }}">
  @csrf
  
  <!-- Hidden metadata -->
  <input type="hidden" name="kpiType" value="...">
  <input type="hidden" name="sectionEmpScoreId" value="...">
  <input type="hidden" name="sectionId" value="...">
  <input type="hidden" name="kpiId" value="...">
  
  <!-- Score input (REQUIRED) -->
  <input type="number" name="employeeScore" value="">
  
  <!-- Comment (Optional) -->
  <textarea name="employeeComment"></textarea>
  
  <!-- Submit button -->
  <button type="submit" class="btn btn-success">Save</button>
</form>
```

### 5. **Initialization Sequence**

1. **DOMContentLoaded event fires**
2. **Determine employee context** (check if same/different employee)
3. **Restore page position** (if same employee, else reset to 0)
4. **Initialize form save states** (`initializeSavedState()`)
5. **Display initial page** (`showPage(currentPage)`)
6. **Update button states** (`updateButtons()`)
7. **Update progress bar** (`updateProgressBar()`)
8. **Attach event listeners** to inputs, buttons, forms
9. **Check for toast messages** 100ms after load
10. **Restore scroll position** if applicable

### 6. **Boundary Conditions**

**First Page:**
- Previous button always disabled
- Can advance only if page passes validation

**Last Page:**
- Next button disabled
- Submit button enabled only if ALL pages valid
- Cannot advance further

**Single Section (totalPages === 1):**
- Previous always disabled
- Next always disabled
- Submit button available after form complete

---

## Implementation Requirements

To replicate this system on another page, ensure:

### **HTML Structure Requirements:**
- ✅ Wrapper elements with class `.section-tab` for each section
- ✅ Forms with class `.ajax-eval-form` for each form
- ✅ Score inputs with `type="number"` and `name` containing "EmpScore"
- ✅ Comment textareas with `name="employeeComment"`
- ✅ Save buttons with `type="submit"`
- ✅ Navigation buttons: `#prev-btn`, `#next-btn`
- ✅ Submit button: `#submit-btn`
- ✅ Display elements: `#current-page`, `#total-pages`, `#progress-bar`

### **Server-side Requirements:**
- ✅ Store workflow status (PENDING, REVIEW, CONFIRMATION, COMPLETED, PROBLEM)
- ✅ Return JSON responses with `{success: bool, message: string}`
- ✅ Handle 401 responses for session expiration
- ✅ Pass employment/entity ID to template

### **JavaScript Dependencies:**
- ✅ SweetAlert2 (Swal) for confirmations and toast notifications
- ✅ Bootstrap modal classes
- ✅ Fetch API (or jQuery AJAX as fallback)
- ✅ SessionStorage API

### **CSS Dependencies:**
- ✅ Bootstrap utility classes (btn-success, btn-secondary, border-warning, etc.)
- ✅ Custom CSS for border-warning styling
- ✅ Progress bar styling

---

## Common State Transition Scenarios

### **Scenario 1: User Fills and Saves Form**
```
1. User opens page → Form state: empty, unsaved → Red border
2. User enters score → Form state: filled, unsaved → Yellow border, Save button active
3. User clicks Save → AJAX POST → Page reloads
4. Page reloads → Form state: filled, saved → No border, Save button disabled
5. Progress bar increments → Visual feedback of progress
```

### **Scenario 2: User Navigates to Next Page**
```
1. Current page invalid (red/yellow border) → Next button disabled
2. User fixes issues → All fields filled & saved → Next button enabled
3. User clicks Next → checkInputs(currentPage) validates → Increment currentPage
4. showPage(currentPage) displays new section range
5. Page position saved to sessionStorage
6. Scroll to new section's top
```

### **Scenario 3: User Switches Employees**
```
1. Viewing employee A on page 2
2. Navigate to employee B form
3. Page loads → Check lastViewedEmployeeId
4. Employee B !== Employee A → Reset currentPage to 0
5. Update lastViewedEmployeeId to employee B
6. showPage(0) displays first 3 sections
```

### **Scenario 4: Supervisor in CONFIRMATION State**
```
1. Status: CONFIRMATION → Form readonly
2. Supervisor scores displayed below employee scores
3. Three action buttons show:
   - Accept → Modal confirmation → Status: COMPLETED
   - Push for Review → Modal confirmation → Status: REVIEW
   - Probe → Direct link → Status: PROBLEM
4. Employee cannot edit fields
5. Pagination controls hidden
```

---

## CSS Classes Reference

| Class | Purpose | Applied To | Condition |
|-------|---------|-----------|-----------|
| `.border-danger` | Red error border | `.section-tab` | Scores empty |
| `.border-warning` | Yellow warning border | `.section-tab` | Unsaved changes |
| `.is-valid` | Green field border | Input/textarea | Field filled |
| `.is-invalid` | Red field border | Input/textarea | Field empty |
| `.btn-success` | Green button (active) | Button | Unsaved/action state |
| `.btn-secondary` | Gray button (disabled) | Button | Saved state |
| `.btn-saved` | Pointer events disabled | Button | Saved state |
| `.progress-bar` | Progress indicator | Div | Fixed top |

---

## Accessibility Features

- ✅ Progress bar: `aria-valuenow`, `aria-valuemin`, `aria-valuemax`
- ✅ Input validation: `.is-valid`, `.is-invalid` states
- ✅ Color-coded feedback supplemented with borders (not color-only)
- ✅ Modal dialogs with proper ARIA attributes
- ✅ Semantic HTML (buttons, forms, sections)
- ✅ Toast notifications via Swal (screen reader compatible)
- ✅ SweetAlert for alerts and confirmations
