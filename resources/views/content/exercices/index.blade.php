@extends('layouts/layoutMaster')

@section('title', __('Mandate'))

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
    @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
    @vite(['resources/assets/vendor/libs/leaflet/leaflet.scss'])
    @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
    @vite(['resources/assets/vendor/libs/leaflet/leaflet.js'])
    @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    <script>
        window.routes = {
            data: "{{ route('mandates.data') }}",
            store: "{{ route('mandates.store') }}",
            update: "{{ route('mandates.update', ['mandate' => ':id']) }}",
            destroy: "{{ route('mandates.destroy', ['mandate' => ':id']) }}",
        }
        window.translations = {
            mandate_name_required: "{{ __('Mandate name is required') }}",
            mandate_logo_required: "{{ __('Mandate logo is required') }}",
            mandate_slogan_required: "{{ __('Mandate slogan is required') }}",
            mandate_year_required: "{{ __('Mandate year is required') }}",
            add_new_mandate: "{{ __('Add New Mandate') }}",
            edit_mandate: "{{ __('Edit Mandate') }}",
            error_occurred: "{{ __('An error occurred') }}",
            submitting: "{{ __('Submitting...') }}",
            failed_to_load_roles: "{{ __('Failed to load roles') }}",
            failed_to_load_members: "{{ __('Failed to load members') }}",
            mandate_delete_title: "{{ __('Mandate delete title') }}",
            mandate_delete_text: "{{ __('Partner delete text') }}",
            mandate_team: "{{ __('Mandate Team') }}",
            add: "{{ __('Add') }}",
            edit: "{{ __('Edit') }}",
            select: "{{ __('Select') }}",
            position_required: "{{ __('Position is required') }}",
            member_required: "{{ __('Member is required') }}",
            team_delete_title: "{{ __('Delete team member?') }}",
            team_delete_text: "{{ __('Are you sure you want to delete this team member?') }}"
        }
    </script>
    @vite(['resources/assets/js/exercices/index.js'])
    @vite(['resources/assets/js/team/index.js'])
    @vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection

@section('content')
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Mandate List') }}</h5>
            @can('mandates.create')
                <button type="button" class="btn btn-primary create-new">
                    <i class="menu-icon icon-base ti tabler-library-plus"></i> {{ __('Add New Mandate') }}
                </button>
            @endcan
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th style="padding-bottom: 40px">#</th>
                        <th>
                            {{ __('Mandate Logo') }} <br>
                            <hr>
                            <small class="text-muted">{{ __('Mandate Name') }}</small>
                        </th>
                        <th style="padding-bottom: 40px">{{ __('Mandate Slogan') }}</th>
                        <th style="padding-bottom: 40px">{{ __('Mandate Year') }}</th>
                        <th style="padding-bottom: 40px">{{ __('Created at') }}</th>
                        <th style="padding-bottom: 40px">{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel"></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="mandateForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="mandate_id" name="mandate_id"
                    value="{{ old('mandate_id', $mandate->string_id ?? 0) }}">
                <div class="col-sm-12 mb-6">
                    <label class="form-label" for="name">{!! showAsterix('name') !!} {{ __('Mandate Name') }}</label>
                    <input type="text" id="name" class="form-control dt-role-name" name="name"
                        placeholder="{{ __('Mandate Name') }}" aria-label="{{ __('Mandate Name') }}"
                        aria-describedby="name" value="{{ old('name', $mandate->name ?? '') }}" required autofocus />
                </div>

                <div class="col-6 mb-6">
                    <label class="form-label" for="year">
                        {!! showAsterix('year') !!}
                        {{ __('Mandate Year') }}</label>
                    <select name="year" id="year" class="selectpicker w-100 select2 form-control"
                        data-style="btn-default" required>
                        <option value="" selected disabled>{{ __('Select a year') }}</option>
                        @for ($i = 1945; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-6 mb-6">
                    <label for="logo" class="form-label"><span id="logo_required_span">{!! showAsterix('logo') !!}
                        </span>{{ __('Mandate Logo') }}</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="" />
                </div>

                <div class="col-sm-12 mb-6">
                    <label class="form-label" for="slogan">{!! showAsterix('slogan') !!} {{ __('Mandate Slogan') }}</label>
                    <input type="text" id="slogan" class="form-control dt-role-slogan" name="slogan"
                        placeholder="{{ __('Mandate Slogan') }}" aria-label="{{ __('Mandate Slogan') }}"
                        aria-describedby="slogan" value="{{ old('slogan', $mandate->slogan ?? '') }}" required />
                </div>

                <hr>

                <div class="row">
                    <div class="row col-12 text-center">
                        <div class="col-md-6 mb-6">
                            <button id="submitBtn" type="submit" class="btn btn-primary">
                                <i class="menu-icon icon-base ti tabler-send"></i> {{ __('Submit') }}
                            </button>
                        </div>
                        <div class="col-md-6 mb-6">
                            <button id="cancelBtn" type="button" class="btn btn-label-danger">
                                <i class="menu-icon icon-base ti tabler-x"></i> {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal to manage team -->
    <div class="offcanvas offcanvas-end" id="manage-team" style="width: 900px;">
        <div class="offcanvas-header border-bottom">
            <h5>
                <span class="offcanvas-team-title"></span> : <span class="offcanvas-team-title-mandate"></span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Mandate Team') }}</h5>
                    <button type="button" class="btn btn-primary create-new-team">
                        <i class="menu-icon icon-base ti tabler-plus"></i> {{ __('Add') }}
                    </button>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-team table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Position') }}</th>
                                <th>{{ __('Member') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal to manage add/edit team -->
    <div class="offcanvas offcanvas-end" id="add-new-record-team-form">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title-team-form"></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="teamForm">
                @csrf
                <input type="hidden" id="team_id" name="team_id" value="0">
                <input type="hidden" id="exercice_id" name="exercice_id" value="0">

                <div class="col-sm-12 mb-6">
                    <label class="form-label" for="role_id">{!! showAsterix('role_id') !!}
                        {{ __('Position') }}</label>
                    <select name="role_id" id="role_id" class="selectpicker w-100 select2" data-style="btn-default"
                        required>
                        <option value="" selected disabled>{{ __('Select') }}</option>
                    </select>
                </div>

                <div class="col-sm-12 mb-6">
                    <label class="form-label" for="member_id">{!! showAsterix('member_id') !!}
                        {{ __('Member') }}</label>
                    <select name="member_id" id="member_id" class="selectpicker w-100 select2" data-style="btn-default"
                        required>
                        <option value="" selected disabled>{{ __('Select') }}</option>
                    </select>
                </div>

                <hr>

                <div class="row">
                    <div class="row col-12 text-center">
                        <div class="col-md-6 mb-6">
                            <button id="submitBtnRegion" type="submit" class="btn btn-primary">
                                <i class="menu-icon icon-base ti tabler-send"></i> {{ __('Submit') }}
                            </button>
                        </div>
                        <div class="col-md-6 mb-6">
                            <button id="cancelBtnRegion" type="button" class="btn btn-label-danger">
                                <i class="menu-icon icon-base ti tabler-x"></i> {{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
