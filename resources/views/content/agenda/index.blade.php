@extends('layouts/layoutMaster')

@section('title', __('Agendas'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
    <style>
        .flatpickr-calendar {
            z-index: 1100 !important;
        }
    </style>
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js'])
@endsection

@section('page-script')
    <script>
        window.translations = {
            retriving_events: "{{ __('Retriving events') }}",
            event_title_required: "{{ __('Even Title is required') }}",
            event_start_date_required: "{{ __('Even Start Date is required') }}",
            event_end_date_required: "{{ __('Even End Date is required') }}",
            event_type_required: "{{ __('Even Type is required') }}",
            submit: "{{ __('Submit') }}",
            update: "{{ __('Update') }}"
        }
    </script>
    @vite(['resources/assets/js/agendas/index.js'])
@endsection

@section('content')
    <div class="card app-calendar-wrapper">
        <div class="row g-0">
            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar">
                <div class="border-bottom p-6 my-sm-0 mb-4">
                    @can('agendas.create')
                        <button class="btn btn-primary btn-toggle-sidebar w-100" data-bs-toggle="offcanvas"
                            data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                            <i class="icon-base ti tabler-plus icon-16px me-2"></i>
                            <span class="align-middle">@lang('Add Event')</span>
                        </button>
                    @endcan
                </div>
                <div class="px-3 pt-2">
                    <!-- inline calendar (flatpicker) -->
                    <div class="inline-calendar"></div>
                </div>
                <hr class="mb-6 mx-n4 mt-3" />
                <div class="px-6 pb-2">
                    <!-- Filter -->
                    <div>
                        <h5>@lang('Event Filters')</h5>
                    </div>

                    <div class="form-check form-check-secondary mb-5 ms-2">
                        <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                            checked />
                        <label class="form-check-label" for="selectAll">@lang('View All')</label>
                    </div>

                    <div class="app-calendar-events-filter text-heading">
                        <div class="form-check form-check-warning mb-5 ms-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-project"
                                data-value="project" checked />
                            <label class="form-check-label" for="select-project">@lang('Projects')</label>
                        </div>
                        <div class="form-check form-check-success mb-5 ms-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-activity"
                                data-value="activity" checked />
                            <label class="form-check-label" for="select-activity">@lang('Activities')</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content">
                <div class="card shadow-none border-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
                <div class="offcanvas offcanvas-end event-sidebar" id="addEventSidebar"
                    aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">@lang('Add Event')</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form class="event-form pt-0" id="eventForm" onsubmit="return false" enctype="multipart/form-data">
                            <div class="mb-5 form-control-validation">
                                <label class="form-label" for="title">{!! showAsterix('title') !!} @lang('Title')</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="@lang('Title')" />
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="banner">@lang('Banner')</label>
                                <input type="file" class="form-control" id="banner" name="banner" accept="image/*" />
                                <div id="banner-preview" class="mt-2 text-center" style="display: none;">
                                    <img src="" class="img-fluid rounded shadow-sm" style="max-height: 150px;"
                                        alt="Banner Preview">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="type">{!! showAsterix('type') !!} @lang('Type')</label>
                                <select class="select2 select-event-label form-select" id="type" name="type">
                                    <option data-label="success" value="activity">@lang('Activities')</option>
                                    <option data-label="warning" value="project">@lang('Projects')</option>
                                </select>
                            </div>
                            <div class="mb-5 form-control-validation">
                                <label class="form-label" for="begin_at">{!! showAsterix('begin_at') !!} @lang('Start Date')</label>
                                <input type="text" class="form-control" id="begin_at" name="begin_at"
                                    placeholder="@lang('Start Date')" />
                            </div>
                            <div class="mb-5 form-control-validation">
                                <label class="form-label" for="end_at">{!! showAsterix('end_at') !!} @lang('End Date')</label>
                                <input type="text" class="form-control" id="end_at" name="end_at"
                                    placeholder="@lang('End Date')" />
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="location">@lang('Location')</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="@lang('Enter Location')" />
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="description">@lang('Description')</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="row col-12 text-center">
                                    <div class="col-md-6 mb-6">
                                        <button id="addEventBtn" type="submit" class="btn btn-primary btn-add-event">
                                            <i class="menu-icon icon-base ti tabler-send"></i> {{ __('Submit') }}
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-6">
                                        <button id="cancelBtn" data-bs-dismiss="offcanvas" type="button"
                                            class="btn btn-label-danger btn-cancel">
                                            <i class="menu-icon icon-base ti tabler-x"></i> {{ __('Cancel') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Calendar & Modal -->
        </div>
    </div>
@endsection
