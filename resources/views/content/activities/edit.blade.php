@extends('layouts/layoutMaster')

@section('title', __('Activity Edit'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/katex.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/dropzone/dropzone.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/tagify/tagify.scss'])
    @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
    @vite(['resources/assets/vendor/libs/leaflet/leaflet.scss'])
    @vite(['resources/assets/vendor/libs/highlight/highlight.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/quill/katex.js', 'resources/assets/vendor/libs/quill/quill.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/dropzone/dropzone.js', 'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/tagify/tagify.js'])
    @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
    @vite(['resources/assets/vendor/libs/leaflet/leaflet.js'])
    @vite(['resources/assets/vendor/libs/highlight/highlight.js'])
@endsection

@section('page-script')
    <script>
        window.routes = {
            activityIndex: "{{ route('activities.index') }}",
            activityStore: "{{ route('activities.store') }}",
            activityUpdate: "{{ route('activities.update', ['activity' => ':id']) }}"
        };

        window.translations = {
            activity_description: "{{ __('Activity description') }}",
            activity_banner_required: "{{ __('Activity banner is required') }}",
            org_pic_invalid: "{{ __('Please select a valid image (JPG, PNG, GIF, WEBP, max 2MB)') }}",
            activity_name_required: "{{ __('Activity name is required') }}",
            activity_place_required: "{{ __('Activity place is required') }}",
            activity_description_required: "{{ __('Activity description is required') }}",
            submitting: "{{ __('Submitting...') }}",
            error_map: "{{ __('Error has occured while trying to get the adresse. Retry Later !') }}",
            retriving_map: "{{ __('We are retriving this adresse') }}",
        };
    </script>
    @vite(['resources/assets/js/activity/form.js'])
    @vite(['resources/assets/js/map-modal.js'])
    @vite(['resources/assets/js/form-validation.js'])
@endsection


@section('content')
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 row-gap-4">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="mb-1">{{ __('Activity Edit') }}</h4>
            <p class="mb-0">
                {!! __('Fields with :asterix are required', ['asterix' => showAsterix('*')]) !!}
            </p>
        </div>
    </div>

    <form id="activityForm" class="needs-validation" enctype="multipart/form-data" novalidate>
        @include('content.activities.partials.form')

        <hr>

        <div class="row">
            <div class="row col-12 text-center">
                <div class="col-md-4 mb-6">
                    <button id="submitBtn" type="button" class="btn btn-primary">
                        <i class="menu-icon icon-base ti tabler-send"></i> {{ __('Submit') }}
                    </button>
                </div>
                <div class="col-md-4 mb-6">
                    <button id="resetBtn" type="reset" class="btn btn-label-warning">
                        <i class="menu-icon icon-base ti tabler-refresh"></i> {{ __('Reset') }}
                    </button>
                </div>
                <div class="col-md-4 mb-6">
                    <button id="cancelBtn" type="button" class="btn btn-label-danger">
                        <i class="menu-icon icon-base ti tabler-x"></i> {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
