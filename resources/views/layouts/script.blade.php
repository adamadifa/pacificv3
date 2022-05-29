<!-- BEGIN: Vendor JS-->
<script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/dropzone.min.js')}}"></script>


<script src="{{asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>


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
<script src="{{asset('app-assets/js/scripts/forms/validation/form-validation.js')}}"></script>
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
    $('body').toggleClass(localStorage.toggled);

    function darkLight() {
        /*DARK CLASS*/
        if (localStorage.toggled != 'dark-layout') {
            $('body').toggleClass('dark-layout', true);
            localStorage.toggled = "dark-layout";

        } else {
            $('body').toggleClass('dark-layout', false);
            localStorage.toggled = "";
        }
    }

    /*Add 'checked' property to input if background == dark*/
    if ($('body').hasClass('dark-layout')) {
        $('#customSwitch11').prop("checked", true)
    } else {
        $('#customSwitch11').prop("checked", false)
    }

</script>
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
            if (width <= 1366) {
                document.body.style.zoom = "70%";
            }
        }
        toggleZoomScreen();
    });

</script>
<!-- END: Page JS-->
@stack('myscript')
