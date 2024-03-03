@extends('frontend.layouts.single')
@section('title', $generatedImage->prompt)
@section('description', $generatedImage->prompt)
@section('og_image', $generatedImage->getThumbnailLink())
@section('content')
    {!! ads_image_page_image_top() !!}
    <section class="section py-0">
        <div class="container">
            <div class="section-inner">
                <div class="section-body">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card-v h-100">
                                <div class="ai-image-img">
                                    <img class="lazy" data-src="{{ $generatedImage->getMainImageLink() }}" class="rounded-3"
                                        alt="{{ $generatedImage->prompt }}">
                                    <div class="spinner-border"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card-v h-100">
                                <div class="ai-image-details">
                                    <div class="ai-image-details-item">
                                        <p class="ai-image-details-title">{{ lang('Prompt', 'image page') }}</p>
                                        <p class="ai-image-details-text text-muted fw-light">{{ $generatedImage->prompt }}
                                        </p>
                                    </div>
                                    @if ($generatedImage->negative_prompt)
                                        <div class="ai-image-details-item">
                                            <p class="ai-image-details-title">{{ lang('Negative Prompt', 'image page') }}
                                            </p>
                                            <p class="ai-image-details-text text-muted fw-light">
                                                {{ $generatedImage->negative_prompt }}
                                            </p>
                                        </div>
                                    @endif
                                    <div class="ai-image-details-item flex-row align-items-center justify-content-between">
                                        <p class="ai-image-details-title mb-0">{{ lang('Created', 'image page') }}</p>
                                        <p class="ai-image-details-text text-muted fw-light text-end">
                                            {{ dateFormat($generatedImage->created_at) }}</p>
                                    </div>
                                    <div class="ai-image-details-item flex-row align-items-center justify-content-between">
                                        <p class="ai-image-details-title mb-0">{{ lang('Views', 'image page') }}</p>
                                        <p class="ai-image-details-text text-muted fw-light text-end">
                                            {{ formatNumber($generatedImage->views) }}
                                            <i class="far fa-eye ms-1"></i>
                                        </p>
                                    </div>
                                    <div class="ai-image-details-item flex-row align-items-center justify-content-between">
                                        <p class="ai-image-details-title mb-0">{{ lang('Downloads', 'image page') }}</p>
                                        <p class="ai-image-details-text text-muted fw-light text-end">
                                            {{ formatNumber($generatedImage->downloads) }}
                                            <i class="fa-solid fa-download ms-1"></i>
                                        </p>
                                    </div>
                                    @if ($generatedImage->visibility)
                                        <div class="ai-image-details-item">
                                            <p class="ai-image-details-title">{{ lang('Share', 'image page') }}</p>
                                            <div class="share mb-3">
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                                    class="social-btn social-facebook" target="_blank">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                                <a href="https://twitter.com/intent/tweet?text={{ url()->current() }}"
                                                    class="social-btn social-twitter" target="_blank">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}"
                                                    class="social-btn social-linkedin" target="_blank">
                                                    <i class="fab fa-linkedin"></i>
                                                </a>
                                                <a href="https://wa.me/?text={{ url()->current() }}"
                                                    class="social-btn social-whatsapp" target="_blank">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                                <a href="http://pinterest.com/pin/create/button/?url={{ url()->current() }}"
                                                    class="social-btn social-pinterest" target="_blank">
                                                    <i class="fab fa-pinterest"></i>
                                                </a>
                                            </div>
                                            <div class="input-group">
                                                <input id="imageLink" type="text"
                                                    class="form-control form-control-md bg-light"
                                                    value="{{ route('images.show', hashid($generatedImage->id)) }}"
                                                    readonly>
                                                <button type="button" class="btn btn-primary btn-md px-3 btn-copy"
                                                    data-clipboard-target="#imageLink">
                                                    <i class="fa-regular fa-clone"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="ai-image-details-item">
                                            <p class="ai-image-details-title">{{ lang('HTML Code', 'image page') }}</p>
                                            <div class="textarea-btn">
                                                <textarea id="htmlCode" class="form-control bg-light" rows="5" readonly><img alt="{{ $generatedImage->prompt }}" src="{{ $generatedImage->getThumbnailLink() }}" /></textarea>
                                                <button class="btn btn-primary btn-copy"
                                                    data-clipboard-target="#htmlCode">{{ lang('Copy', 'image page') }}</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="ai-image-details-item">
                                            <div class="alert alert-danger mb-0" role="alert">
                                                <i class="fa-regular fa-circle-question me-2"></i>
                                                {{ lang('This Image is private It cannot be shared', 'image page') }}
                                            </div>
                                        </div>
                                    @endif
                                    <div class="ai-image-details-item">
                                        <a href="{{ route('images.download', [hashid($generatedImage->id), $generatedImage->getMainImageName()]) }}"
                                            class="btn btn-primary btn-md w-100 mb-3">{{ lang('Download', 'image page') }}</a>
                                        @if (subscription())
                                            <a href="{{ route('home', 'prompt=' . $generatedImage->prompt) }}"
                                                class="btn btn-light btn-md w-100">{{ lang('Generate similar', 'image page') }}</a>
                                        @endif
                                        @if (auth()->user() && $generatedImage->user_id == auth()->user()->id)
                                            <form class="mt-3 w-100"
                                                action="{{ route('user.gallery.destroy', hashid($generatedImage->id)) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="btn btn-danger btn-md action-confirm w-100">{{ lang('Delete', 'image page') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {!! ads_image_page_center() !!}
    @include('frontend.includes.faqs')
    @include('frontend.includes.articles')
    {!! ads_image_page_image_bottom() !!}
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/aos/aos.min.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('assets/vendor/libs/aos/aos.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/jquery/jquery.lazy.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/clipboard/clipboard.min.js') }}"></script>
    @endpush
@endsection
