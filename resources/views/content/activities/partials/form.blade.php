@csrf
<div class="row">
    <!-- First column-->
    <div class="col-12 col-lg-12 mb-6">
        <!-- Activity Information -->
        <div class="card mb-1">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Main information') }}</h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="activity_id" value="{{ $activity->string_id ?? null }}">
                <div class="mb-6">
                    <label class="form-label" for="name">{!! showAsterix('name') !!}
                        {{ __('Activity name') }}</label>
                    <input type="text" class="form-control" id="name" placeholder="{{ __('Activity name') }}"
                        name="name" value="{{ old('name', $activity->name ?? '') }}" autofocus required />
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-6">
                            <label class="form-label" for="place">{!! showAsterix('place') !!}
                                {{ __('Activity place') }}</label>
                            <div class="input-group">
                                <input type="hidden" id="latitude" name="latitude"
                                    value="{{ old('latitude', $activity->latitude ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude"
                                    value="{{ old('longitude', $activity->longitude ?? '') }}">
                                <input type="text" class="form-control" id="place"
                                    placeholder="{{ __('Activity place') }}" name="place"
                                    value="{{ old('place', $activity->place ?? '') }}" required />
                                <button type="button" id="btnLocate" class="btn btn-outline-secondary"
                                    title="Localiser">
                                    <i class="menu-icon icon-base ti tabler-map-pin"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-6">
                        <label for="banner" class="form-label">
                            @if (!isset($activity))
                                <span id="pic_required_span">{!! showAsterix('banner') !!} </span>
                            @endif
                            {{ __('Activity Banner') }}
                        </label>
                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*" />
                        @if (isset($activity) && $activity->banner)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $activity->banner) }}" alt="Activity Banner"
                                    class="img-thumbnail" style="max-width: 200px; max-height: 200px;"
                                    id="currentbanner">
                            </div>
                        @endif
                        <div class="mt-2" id="bannerPreview" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail"
                                style="max-width: 200px; max-height: 200px;" id="previewImage">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-6">
                        <label for="annexes" class="form-label">
                            {{ __('Image Annexes') }}
                        </label>
                        <input type="file" class="form-control" id="annexes" name="annexes[]" accept="image/*"
                            multiple />
                        <small class="text-muted">{{ __('You can select multiple images') }}</small>

                        @if (isset($activity) && $activity->images->count() > 0)
                            <div class="mt-3">
                                <label class="form-label">{{ __('Existing Images') }}</label>
                                <div class="row g-2" id="existingImages">
                                    @foreach ($activity->images as $image)
                                        <div class="col-6 col-md-4 col-lg-3" data-image-id="{{ $image->string_id }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                    alt="Activity Image" class="img-thumbnail w-100"
                                                    style="height: 150px; object-fit: cover;">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 btn-delete-image"
                                                    data-image-id="{{ $image->string_id }}"
                                                    title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="deleted_images" id="deletedImages" value="">
                            </div>
                        @endif

                        <div class="mt-3" id="annexesPreview" style="display: none;">
                            <label class="form-label">{{ __('New Images Preview') }}</label>
                            <div class="row g-2" id="previewContainer"></div>
                        </div>
                    </div>
                </div>

                <!-- Snow Theme -->
                <div class="mb-6">
                    <label class="form-label" for="description">{!! showAsterix('description') !!}
                        {{ __('Activity description') }}</label>
                    <div id="snow-editor"></div>
                    <input type="hidden" name="description" id="description"
                        value="{{ old('description', $activity->description ?? '') }}">
                </div>
            </div>
        </div>
        <!-- /Activity Information -->
    </div>

    {{-- <!-- Second column -->
    <div class="col-12 col-lg-4">
        <!-- Orgaisation Social Card -->
        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Social networks') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-6">
                    </div>
                    <div class="col-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Orgaisation Social Card -->
    </div>
    <!-- /Second column --> --}}
</div>

<x-map-modal></x-map-modal>
