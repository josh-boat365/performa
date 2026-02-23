# Employee KPI Form - State Diagrams & Flow Charts

This document provides visual representations of state transitions and user workflows.

---

## 1. Overall Application State Machine

```
┌─────────────────────────────────────────────────────────────────────┐
│                      OVERALL WORKFLOW STATES                         │
└─────────────────────────────────────────────────────────────────────┘

                              START
                                │
                                ▼
                    ┌─────────────────────┐
                    │   PENDING STATUS    │
                    │  (Employee Editing) │
                    └──────────┬──────────┘
                               │
                       ┌───────┴────────┐
                       │                │
                       ▼                ▼
            ┌──────────────────┐   User Clicks
            │  Fill All Forms  │   "Submit"
            │  (3 items/page)  │
            └──────────────────┘   (Validation)
                       │                │
                       └────────┬───────┘
                                ▼
                    ┌─────────────────────┐
                    │  REVIEW STATUS      │
                    │ (Supervisor Review) │
                    │ (Form Readonly)     │
                    └──────────┬──────────┘
                               │
                        ┌──────┴──────┐
                        │             │
                        ▼             ▼
            ┌─────────────────┐  ┌──────────────────┐
            │ CONFIRMATION    │  │ PROBLEM          │
            │ (Employee Acts) │  │ (Probe Required) │
            └────────┬────────┘  └──────────────────┘
                     │
            ┌────────┴─────────┐
            │                  │
            ▼                  ▼
        Accept            Push for Review
            │                  │
            ▼                  ▼
    ┌──────────────┐  ┌──────────────────┐
    │ COMPLETED    │  │ REVIEW           │
    │ (Finalized)  │  │ (Back to Review) │
    └──────────────┘  └──────────────────┘
                            │
                            └─── (Cycle continues)
```

---

## 2. Section/Form State Machine

```
┌──────────────────────────────────────────────────────────────────────┐
│                    INDIVIDUAL FORM STATE MACHINE                      │
└──────────────────────────────────────────────────────────────────────┘

                          FORM INITIALIZED
                                │
                    ┌───────────┴───────────┐
                    │                       │
                    ▼                       ▼
            ┌───────────────┐      ┌──────────────────┐
            │ PRE-FILLED    │      │ EMPTY            │
            │ (Has Value)   │      │ (No Value)       │
            └───────┬───────┘      └────────┬─────────┘
                    │                       │
                    │               User Types Score
                    │                       │
                    │       ┌───────────────┘
                    │       │
                    ▼       ▼
            ┌──────────────────────┐
            │  FILLED & UNSAVED    │
            │  Button: "Save"      │
            │  Border: YELLOW      │
            └──────────┬───────────┘
                       │
                User Clicks Save
              (AJAX POST triggered)
                       │
                       ▼
            ┌──────────────────────┐
            │  SAVING...           │
            │  Button: Disabled    │
            │  Spinner: Showing    │
            └──────────┬───────────┘
                       │
            ┌──────────┴─────────────┐
            │                        │
            ▼                        ▼
    ┌────────────────┐      ┌──────────────┐
    │ SUCCESS        │      │ ERROR        │
    │ Page Reloads   │      │ Page Reloads │
    └────────┬───────┘      └──────┬───────┘
             │                     │
             ▼                     ▼
    ┌────────────────────┐  ┌──────────────────────┐
    │ FILLED & SAVED     │  │ FILLED & UNSAVED     │
    │ Button: "Saved"    │  │ Button: "Save"       │
    │ Border: NONE       │  │ Border: YELLOW       │
    │ (Disabled Button)  │  │ User re-attempts save
    └────────────────────┘  └──────────────────────┘
             │                     │
             │                     └──→ (Loop back to SAVING...)
             │
             └──────→ (Form now valid for page navigation)
```

---

## 3. Page Validation State

```
┌──────────────────────────────────────────────────────────────────────┐
│                     PAGE VALIDATION STATE FLOW                        │
└──────────────────────────────────────────────────────────────────────┘

                     PAGE DISPLAYED (3 sections)
                              │
                ┌─────────────┴──────────────┐
                │                            │
                ▼                            ▼
        ┌──────────────┐          ┌──────────────┐
        │ Check Scores │          │ Check Saved  │
        └──────┬───────┘          └──────┬───────┘
               │                         │
        ┌──────┴─┬──────┐         ┌──────┴──────┐
        │         │      │         │             │
    Empty?   Filled?   Mixed?   All Saved?   Some Unsaved?
        │         │      │         │             │
        ▼         ▼      ▼         ▼             ▼
      Yes       Yes     No/Yes    Yes          No
        │         │      │         │             │
        ▼         ▼      ▼         ▼             ▼
    ┌────────────────────┐    ┌──────────────┐  ┌──────────────┐
    │ STATE: INCOMPLETE  │    │ STATE: VALID │  │ STATE: UNSAV │
    │ Border: RED ────┐  │    │ Border: NONE │  │ Border: YEL  │
    │ Next Disabled  │  │    │ Next Enabled │  │ Next Disabled│
    │ Submit Disabled│  │    │ Submit Check │  │ Submit Check │
    └────────────────────┘    └──────────────┘  └──────────────┘
    (Cannot advance)         (Can advance)    (Cannot advance)
```

---

## 4. Button State Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                       BUTTON STATE BEHAVIORS                            │
└────────────────────────────────────────────────────────────────────────┘

PREVIOUS BUTTON (#prev-btn)
───────────────────────────
                Current Page
                     │
        ┌────────────┴────────────┐
        │                         │
        │ = 0                     │ > 0
        │                         │
        ▼                         ▼
    DISABLED              ENABLED
   (First Page)        (Any Other Page)
        │                    │
        │               User Clicks
        │                    │
        │                    ▼
        │            currentPage--
        │            showPage()
        │                    │
        └────────┬───────────┘
                 │
            Update All States


NEXT BUTTON (#next-btn)
──────────────────────
            currentPage
                │
        ┌───────┴────────┐
        │                │
    = Last           < Last
        │                │
        ▼                ▼
    AND               AND
    Current Page      Current Page
    is Valid?         is Valid?
        │                │
    ┌───┴───┐        ┌───┴───┐
    │       │        │       │
   No/Yes  No/Yes   Yes     No
    │      │        │       │
DISABLED  DISABLED  ENABLED DISABLED
 (Wait)    (Wait)  (Proceed) (Wait)
              │                │
              └────────┬───────┘
                User Clicks
                   │
                   ▼
            validatePage()
             │       │
         invalid  valid
             │       │
             ▼       ▼
        Error    currentPage++
        Alert    showPage()


SAVE BUTTON (Per Form)
──────────────────────
        Has Pre-filled Value?
              │
        ┌─────┴─────┐
        │           │
       YES         NO
        │           │
        ▼           ▼
    Saved State  Unsaved State
    ─────────    ──────────────
    button="Saved"  button="Save"
    class+="btn-secondary"   class+="btn-success"
    disabled=true   disabled=false
    cursor=not-allowed  cursor=pointer
        │           │
        │      User Types
        │           │
        │           ▼
        │       Unsaved State
        │           │
        │      User Clicks Save
        │      (AJAX pending)
        │           │
        │           ▼
        │       Saving State
        │       button="Saving..."
        │       disabled=true
        │       spinner=show
        │           │
        ├────┬──────┤
        │    │      │
    Success  │   Error
        │    │      │
        ▼    ▼      ▼
    Reload Page   Reload Page
        │              │
        └────┬──────────┘
             │
        Re-evaluate form state
        based on server data


SUBMIT BUTTON (#submit-btn)
───────────────────────────
        All Pages Valid?
        (All checkInputs(i) true)
              │
        ┌─────┴──────┐
        │            │
       YES          NO
        │            │
        ▼            ▼
    ENABLED      DISABLED
        │            │
    User Clicks   (Wait user)
        │
        ▼
    Modal Opens
        │
    ┌───┴────┐
    │        │
  Confirm  Cancel
    │        │
    ▼        ▼
Form Submit Modal Closes
    │        │
    │        └─→ checkInputs continues
    │
    ▼
POST /submit
    │
    ▼
Redirect or Error
```

---

## 5. Pagination Flow

```
┌────────────────────────────────────────────────────────────────────────┐
│                      PAGINATION STATE FLOW                              │
└────────────────────────────────────────────────────────────────────────┘

LOAD PAGE (New Employee/Record)
       │
       ▼
Check SessionStorage
'lastViewedRecordId'
       │
    ┌──┴──────────────────┐
    │                     │
 exists           doesn't exist
 same ID?              OR
    │              different ID
    │                   │
   YES                 NO
    │                   │
    ▼                   ▼
Restore Page      Reset to 0
Position          Set lastViewedRecordId
from Storage      Clear page storage
    │                   │
    └─────────┬─────────┘
              │
              ▼
        showPage(0)
           │
    ┌──────┼─────────┐
    │      │         │
    ▼      ▼         ▼
Display  Update   Scroll to
Sections Buttons  First Section
    │
    ▼
USER INTERACTION
    │
    ┌────────────────┬──────────────┐
    │                │              │
 Click Prev       Click Next    (Other Event)
    │                │              │
    ▼                ▼              ▼
Check if          Check if      updateButtons()
currentPage > 0   checkInputs()
    │                │
 ┌──┴──┐          ┌──┴──┐
 │     │          │     │
YES    NO        YES     NO
 │     │          │      │
 ▼     ▼          ▼      ▼
Dec    Do       Inc     Don't  
Page  Nothing   Page   Advance
 │                │     │
 ▼                ▼     ▼
currentPage--  currentPage++  Disable Button
showPage() showPage()   Show Validation
    │                │        Message
    │                │
    ▼                ▼
SessionStorage.setItem()
(Store current page position)
    │
    ▼
showPage(currentPage)
    │
    ├─── Hide all sections
    │
    ├─── Show sections[start:end]
    │
    ├─── Update current-page span
    │
    ├─── updateButtons()
    │
    └─── Smooth scroll to section
```

---

## 6. Form Save & Reload Cycle

```
┌────────────────────────────────────────────────────────────────────────┐
│              AJAX SAVE & PAGE RELOAD STATE CYCLE                        │
└────────────────────────────────────────────────────────────────────────┘

USER CLICKS SAVE
     │
     ▼
form.addEventListener('submit')
     │
     ▼
e.preventDefault()
     │
     ▼
Save Pre-Submission State
├─ ScrollPos = window.scrollY
├─ CurrentPage to sessionStorage
└─ Button shows "Saving..."
     │
     ▼
Create FormData
(employeeScore, comment, etc.)
     │
     ▼
fetch(POST) with CSRF token
     │
     ├─────────────┬────────────────┬──────────────────┐
     │             │                │                  │
     ▼             ▼                ▼                  ▼
  Status 200    Status 401      Status 422         Network Error
   Success   (Session Exp)      (Validation)        (Offline)
     │             │                │                  │
     ▼             ▼                ▼                  ▼
JSON Parse Redirect to       JSON Parse       console.log()
     │       Login Page           │
response.json() │            response.json()  Store Error
     │             │                │          in Session
     ▼             │                ▼          │
{success: true, ▼          {success: false,   ▼
 message: "..."} STOP       message: "..."}  Store
     │                            │          Error
     ▼                            ▼
Store Success            Store Error Toast
Toast Data               Data in sessionStorage
in sessionStorage             │
     │                        ▼
     ├────────────┬───────────┤
     │            │           │
     ▼            ▼           ▼
window.location.reload()
     │
     ▼
PAGE RELOADS
     │
     ▼
DOMContentLoaded fires
     │
     ├─── initializeSavedState()
     │    (Check pre-filled values)
     │
     ├─── showPage(currentPage)
     │
     └─── setTimeout 100ms
         │
         ▼
    Restore Scroll Position
    (from sessionStorage)
         │
         ▼
    Check for Toast Messages
    (from sessionStorage)
         │
    ┌────┴────┐
    │         │
Success     Error
    │         │
    ▼         ▼
Show Swal   Show Swal
Toast       Toast
(green)     (red)
    │         │
    ▼         ▼
Clear       Clear
Storage     Storage
    │         │
    └────┬────┘
         │
         ▼
USER SEES:
✓ Updated form state
✓ Same page position
✓ Same scroll position
✓ Success/Error notification
```

---

## 7. Complete User Journey (Happy Path)

```
┌────────────────────────────────────────────────────────────────────────┐
│           COMPLETE USER JOURNEY - HAPPY PATH (PENDING → COMPLETED)      │
└────────────────────────────────────────────────────────────────────────┘

1. EMPLOYEE OPENS FORM
   ┌─────────────────────────────────────────────┐
   │ Page Load                                   │
   │ ├─ Status: PENDING                         │
   │ ├─ Pagination shows: Page 1 of 4           │
   │ ├─ Progress bar: 0%                        │
   │ ├─ 3 sections visible (sections 1-3)       │
   │ ├─ All inputs empty                        │
   │ └─ Prev/Next/Submit buttons: DISABLED      │
   └─────────────────────────────────────────────┘

2. SECTION 1: FILL SCORE & SAVE
   ┌─────────────────────────────────────────────┐
   │ Employee enters score: 85                   │
   │ ├─ Input: Green border (.is-valid)         │
   │ ├─ Section: Yellow border (.border-warning)│
   │ ├─ Save button: Green, "Save"              │
   │ ├─ Next button: Still disabled             │
   │ └─ submitBtn.disabled = !all pages valid   │
   └─────────────────────────────────────────────┘

3. SECTION 1: CLICK SAVE BUTTON
   ┌─────────────────────────────────────────────┐
   │ Save Button State:                          │
   │ ├─ "Saving..."                              │
   │ ├─ Spinner spinning                         │
   │ ├─ Button disabled                          │
   │ ├─ AJAX POST to /save                       │
   │ └─ Server saves score to DB                 │
   └─────────────────────────────────────────────┘

4. SAVE COMPLETES, PAGE RELOADS
   ┌─────────────────────────────────────────────┐
   │ Post-Page-Load Toast Shows:                 │
   │ "✓ Saved successfully"                      │
   │                                             │
   │ Form State Restored:                        │
   │ ├─ Section 1: Yellow → No border           │
   │ ├─ Save button: "Saved", gray, disabled    │
   │ ├─ Input: Green border (filled)            │
   │ ├─ Progress bar: 25% (1 of 4 sections)     │
   │ └─ Next button: ENABLED                    │
   └─────────────────────────────────────────────┘

5. REPEAT FOR SECTIONS 2-3 (Page 1)
   After each save:
   │ ├─ Border: Yellow → None
   │ ├─ Button: "Save" → "Saved"
   │ ├─ Progress bar: Increments
   │ └─ Next button: Stays enabled if page valid
   │

6. CLICK "NEXT" BUTTON (Page 1 → Page 2)
   ┌─────────────────────────────────────────────┐
   │ Pre-navigation Validation:                  │
   │ ├─ All 3 sections filled? ✓               │
   │ ├─ All 3 sections saved? ✓                │
   │ ├─ Returns: true → proceed                 │
   │                                             │
   │ Page Transition:                            │
   │ ├─ currentPage = 1                          │
   │ ├─ Sections 1-3: Hidden                    │
   │ ├─ Sections 4-6: Displayed                 │
   │ ├─ Page indicator: "2 / 4"                 │
   │ ├─ Scroll: Smooth to section 4             │
   │ └─ State: Page 1 stored in sessionStorage  │
   └─────────────────────────────────────────────┘

7. FILL & SAVE SECTIONS 4-6 (Same as 1-3)
   Repeat the fill/save/validate cycle for page 2
   │ ├─ Progress bar continues: 50% → 75%
   │ └─ Next button enabled after all saved

8. CLICK "NEXT" BUTTON (Page 2 → Page 3)
   Same validation & transition

9. CLICK "NEXT" BUTTON (Page 3 → Page 4)
   But currentPage = totalPages - 1, so:
   ├─ Next disabled already
   ├─ Last page visible
   └─ Submit button now enabled (all pages valid)

10. CLICK "SUBMIT APPRAISAL" BUTTON
    ┌─────────────────────────────────────────────┐
    │ Modal opens: Confirmation dialog            │
    │ "Are you sure? Submit for review?"         │
    │ └─ [Confirm] [Cancel]                      │
    └─────────────────────────────────────────────┘

11. EMPLOYEE CONFIRMS SUBMISSION
    ┌─────────────────────────────────────────────┐
    │ Form submission:                            │
    │ ├─ POST /submit                             │
    │ ├─ Status updated: PENDING → REVIEW        │
    │ ├─ Notification sent to supervisor         │
    │ ├─ Page redirects or reloads               │
    │ └─ Toast: "✓ Submitted for review"        │
    └─────────────────────────────────────────────┘

12. SUPERVISOR REVIEWS & SCORES
    (Form now shows status: REVIEW)
    ├─ Pagination: Hidden
    ├─ All inputs: Readonly
    ├─ Employee sees supervisor scores
    └─ Status: "REVIEW" (warning badge)

13. STATUS CHANGES TO CONFIRMATION
    ┌─────────────────────────────────────────────┐
    │ Supervisor submitted their review          │
    │ Status: REVIEW → CONFIRMATION              │
    │                                             │
    │ Employee sees three buttons:                │
    │ ├─ [Accept] → Status: COMPLETED           │
    │ ├─ [Push for Review] → Status: REVIEW     │
    │ └─ [Probe] → View probing section         │
    └─────────────────────────────────────────────┘

14. EMPLOYEE ACCEPTS SUPERVISOR SCORES
    ┌─────────────────────────────────────────────┐
    │ Modal confirmation opens                    │
    │ "Accept these scores?"                     │
    │ └─ [Confirm] [Cancel]                      │
    └─────────────────────────────────────────────┘

15. CONFIRMATION COMPLETE
    ┌─────────────────────────────────────────────┐
    │ Status: CONFIRMATION → COMPLETED           │
    │ ├─ All inputs: Readonly                    │
    │ ├─ Buttons: Hidden                         │
    │ ├─ Badge: Green "COMPLETED"               │
    │ └─ Toast: "✓ Appraisal completed"        │
    └─────────────────────────────────────────────┘

END: Appraisal cycle complete
```

---

## 8. Error Recovery Flows

```
┌────────────────────────────────────────────────────────────────────────┐
│                      ERROR RECOVERY SCENARIOS                           │
└────────────────────────────────────────────────────────────────────────┘

SCENARIO: Session Expires (401 Error)
─────────────────────────────────────
User filling form → Click Save
         │
         ▼
    Fetch Request
    401 Response received
         │
         ▼
    response.json()
         │
         ▼
    Check: session_expired = true?
         │
         ▼
    YES - Redirect:
    alert('Your session has expired.')
    window.location.href = '/login'
     │
     └─→ Loop back to login flow


SCENARIO: Validation Error (422 Error)
──────────────────────────────────────
User fills invalid data → Click Save
         │
         ▼
    Fetch Request
    422 Response (validation failed)
         │
         ▼
    response.json()
    {success: false, message: "...error..."}
         │
         ▼
    Store Error Toast
    sessionStorage.setItem('showErrorToast', ...)
         │
         ▼
    window.location.reload()
         │
         ▼
    Error Toast Shows:
    "✗ Validation failed: ..."
         │
         ▼
    User corrects input
    Attempts save again


SCENARIO: Network Error
──────────────────────
User tries to save → Network goes offline
         │
         ▼
    fetch().catch()
    Network error caught
         │
         ▼
    Store Error Toast
    "An unexpected error occurred"
         │
         ▼
    window.location.reload()
         │
         ▼
    Error Toast Shows
         │
         ▼
    User fixes network
    Retries save


SCENARIO: Page Refreshes During Save
────────────────────────────────────
User clicks Save
SessionStorage populated with:
├─ preserveScrollPosition
├─ currentPage position
└─ (toast data from response)
         │
         ▼
    Page Reloads (auto or manual)
         │
         ▼
    100ms setTimeout executes:
    ├─ Restore scroll position
    ├─ Show toast if available
    └─ Re-initialize form states
         │
         ▼
    User sees everything restored


SCENARIO: User Switches Employees (Same Session)
────────────────────────────────────────────────
Session Storage:
lastViewedEmployeeId = 1
currentPage_employee_1 = 2

User opens Employee 2 form
         │
         ▼
Check: lastViewedEmployeeId === employeeId?
    NO (1 !== 2)
         │
         ▼
Reset currentPage = 0
Update lastViewedEmployeeId = 2
         │
         ▼
showPage(0)
         │
         ▼
User sees Employee 2 from Page 1
(No confusion with Employee 1's page position)
         │
         ▼
User navigates to Employee 1
Check: lastViewedEmployeeId === employeeId?
    YES (1 === 1)
         │
         ▼
Restore currentPage from storage
         │
         ▼
showPage(savedPage)
         │
         ▼
User returns to Employee 1's Page 2
```

---

## 9. State Indicator Key

### Visual Indicators at a Glance

**Section Borders:**
```
RED Border (#border-danger)        → Scores are EMPTY
                                     Action: Fill all score fields
                                     Next Button: DISABLED

YELLOW Border (#border-warning)    → Scores filled but NOT SAVED
                                     Action: Click Save button
                                     Next Button: DISABLED

NO Border                          → Scores filled AND SAVED
                                     Status: VALID
                                     Next Button: ENABLED
```

**Input Field Borders:**
```
GREEN Border (.is-valid)           → Field has value
                                     Status: Ready to save

RED Border (.is-invalid)           → Field is empty
                                     Status: Needs input
```

**Save Button Colors:**
```
GREEN Button (.btn-success)        → Unsaved changes present
                                     Action: Clickable, shows "Save"
                                     State: form.dataset.saved = 'false'

GRAY Button (.btn-secondary)       → Changes saved
                                     Action: Disabled, shows "Saved"
                                     State: form.dataset.saved = 'true'

SPINNER + "Saving..." Text         → Save in progress
                                     Action: Disabled, shows spinner
```

**Navigation Buttons:**
```
BLUE (enabled) #next-btn           → Current page valid
                                     Click to proceed to next

DARK (disabled) #next-btn          → Current page invalid
                                     Cannot proceed

DARK (disabled) #prev-btn          → On first page
                                     Cannot go back

BLUE (enabled) #prev-btn           → Not on first page
                                     Can go back
```

**Progress Bar:**
```
Width: 0% - 100%                   → Percentage of valid sections
Position: Fixed to top             → Always visible
Color: Blue striped, animated      → Shows progress

Example:
0% = No sections completed
25% = 1 of 4 sections done
50% = 2 of 4 sections done
100% = All 4 sections done
```

**Status Badge:**
```
DARK (gray) "PENDING"              → Employee hasn't submitted
                                     Form: Editable

YELLOW "REVIEW"                    → Supervisor reviewing
                                     Form: Readonly

BLUE "CONFIRMATION"                → Waiting for employee response
                                     Buttons: Accept/Push/Probe

GREEN "COMPLETED"                  → Appraisal finalized
                                     Form: Readonly view only

RED "PROBE"                         → Flagged for further review
                                     Form: Readonly
```

---

## 10. State Transition Matrix

| Current Form State | Event | Next State | Button Change | Border Change | Next Button |
|------------------|-------|-----------|---------------|---------------|-----------|
| Empty | Type score | Unsaved | "Save" (green) | Yellow | Disabled |
| Unsaved | Click Save | Saving | "Saving..." (disabled) | Yellow | Disabled |
| Saving | Success + Reload | Saved | "Saved" (gray) | None | Check page |
| Saved | Edit score | Unsaved | "Save" (green) | Yellow | Disabled |
| Unsaved | Change back to original | Saved | "Saved" (gray) | None | Check page |
| Saved | Click Previous/Next | (no change) | (no change) | (no change) | (no change) |
| Page Incomplete | Try Next | (blocked) | (no change) | (no change) | Dialog shown |
| Page Complete | Click Next | Page 2 | (no change) | (no change) | currentPage++ |

---

## 11. Data Flow Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                         DATA FLOW DIAGRAM                               │
└────────────────────────────────────────────────────────────────────────┘

FRONTEND                              BACKEND                  DATABASE
═══════════════════════════════════════════════════════════════════════════

User Opens Page
    │
    ▼
Request /evaluation/1
    │────────────────────────────► Controller->show()
    │                                   │
    │                                   ▼
    │                         Query evaluations table
    │                         Join evaluation_items
    │                         Eager load relationships
    │                                   │
    │                                   ▼
    │                         Return view with data
    │◄────────────────────────┤
    │
    ▼
Blade template renders
├─ Loop through $items
├─ Load saved scores from DB
├─ Set form.dataset.saved based on data
└─ Initialize JavaScript


USER FILLS FORM (Client-side)
    │
    ▼
Score input changed
    │
    ├─ validateField()
    ├─ markFormUnsaved()
    ├─ updateButtons()
    └─ updateProgressBar()
    │
    ▼ (In-memory only)
No server request yet


USER CLICKS SAVE
    │
    ▼
Collect FormData
├─ employeeScore
├─ employeeComment
├─ itemId
├─ recordId
└─ CSRF token
    │────────────────────────────► POST /evaluation/save
    │                                   │
    │                                   ▼
    │                         Validate input
    │                         Find or Create record
    │                         Update/Insert in DB
    │                                   │
    │                                   ▼
    │                         Return JSON response
    │                    {success: true, message: "..."}
    │◄────────────────────────┤
    │
    ▼
Store response in sessionStorage
window.location.reload()


PAGE RELOADS
    │
    ▼ (Server fetches fresh data)
Request /evaluation/1 again
    │────────────────────────────► Controller->show()
    │                                   │
    │                                   ▼
    │                         Query DB with fresh data
    │                         (Now includes saved score)
    │                                   │
    │                                   ▼
    │                         Return view
    │◄────────────────────────┤
    │
    ▼
Blade renders with updated values
├─ Score input: value="85" (now pre-filled)
├─ Form state: dataset.saved = 'true' (since has value)
└─ Save button: "Saved", disabled


INITIALIZE JAVASCRIPT
    │
    ├─ initializeSavedState()
    │  └─ Check all pre-filled values
    │     └─ Set dataset.saved accordingly
    │
    ├─ showPage()
    │  └─ Display current section
    │
    ├─ updateButtons()
    │  └─ Enable/disable based on state
    │
    └─ Check toast messages
       ├─ Show success/error toast
       └─ Clear sessionStorage


USER CLICKS SUBMIT APPRAISAL
    │
    ▼
Modal opens (client-side only)
User confirms
    │────────────────────────────► POST /evaluation/submit
    │                                   │
    │                                   ▼
    │                         Update status: PENDING -> REVIEW
    │                         Save timestamp & user_id
    │                         Send notification to supervisor
    │                                   │
    │                                   ▼
    │                         Return JSON with redirect
    │◄────────────────────────┤
    │
    ▼
Redirect to show page
    │
    └───────→ (Repeat cycle)
```

