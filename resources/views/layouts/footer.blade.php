<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

<!-- form repeater js -->
<script src="{{ asset('assets/libs/jquery.repeater/jquery.repeater.min.js') }}"></script>

<script src="{{ asset('assets/js/pages/form-repeater.int.js') }}"></script>

<!-- jquery step -->
<script src="{{ asset('assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>

<!-- form wizard init -->
<script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>

<!-- apexcharts -->
{{--  <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>  --}}

<!-- apexcharts init -->
{{--  <script src="{{ asset('assets/js/pages/apexcharts.init.js') }}"></script>  --}}

<!-- custom dashboard chart init -->
<script src="{{ asset('assets/custom/dashboard-charts.init.js') }}"></script>

<script src="{{ asset('assets/js/plugin.js') }}"></script>

<!-- dashboard init -->
<script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>

<!-- rating -->
<script src="{{ asset('assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

<script src="{{ asset('assets/js/pages/rating-init.js') }}"></script>

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Sweet alert init js-->
<script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>



<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>


@push('scripts')
    <script>
        // SWEETALERT CONFIRM DELETE
        function confirmDelete(button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Get the form that contains the button clicked
                    button.closest('form').submit();
                }
            });
        }
    </script>
    
@endpush
