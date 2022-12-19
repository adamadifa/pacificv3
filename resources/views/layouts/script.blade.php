<!-- BEGIN: Vendor JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>

<script src="{{asset('app-assets/vendors/js/extensions/dropzone.min.js')}}"></script>


<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>

<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('app-assets/js/core/app.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/components.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts//popover/popover.js') }}"></script>
<!-- END: Theme JS-->
<!-- BEGIN: Page JS-->
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/ui/data-list-view.js')}}"></script>
<script src="{{asset('app-assets/js/jquery.maskMoney.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>

<!-- END: Page JS-->

<script src="{{ asset('app-assets/js/external/jquery.mask.min.js') }}"></script>
<script src="{{ asset('app-assets/js/external/sweetalert.min.js') }}"></script>
<script src="{{ asset('app-assets/js/external/jquery-ui.js') }}"></script>

<!-- BEGIN: Page JS-->

<script src="{{asset('app-assets/js/scripts/datatables/datatable.js')}}"></script>

<script src="{{ asset('app-assets/js/external/highcharts.js') }}"></script>
<script src="{{ asset('app-assets/js/external/series-label.js') }}"></script>
<script src="{{ asset('app-assets/js/external/exporting.js') }}"></script>
<script src="{{ asset('app-assets/js/external/export-data.js') }}"></script>
<script src="{{ asset('app-assets/js/external/accessibility.js') }}"></script>
<script src="{{ asset('app-assets/js/external/selectize.js') }}"></script>
<script src="{{ asset('app-assets/js/customizer.min.js') }}"></script>
<script src="{{ asset('app-assets/js/external/feather.min.js') }}"></script>
<script>
    feather.replace();

</script>
<script>
    $(window).on("load", function() {
        $(".loader-wrapper").fadeOut("slow");
    });

</script>

<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<script src="{{ asset('app-assets/signature/signature.js') }}"></script>

<script>
    $(function() {
        //fix modal force focus

        $.fn.modal.Constructor.prototype.enforceFocus = function() {
            var that = this;
            $(document).on('focusin.modal', function(e) {
                if ($(e.target).hasClass('select2')) {
                    return true;
                }

                if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
                    that.$element.focus();
                }
            });
        };

        function toggleZoomScreen() {
            var width = window.screen.width;

            if (width <= 1366 && width >= 1024 && width != 800) {
                document.body.style.zoom = "70%";
            }
        }
        toggleZoomScreen();
    });

</script>
<!-- END: Page JS-->
@stack('myscript')
