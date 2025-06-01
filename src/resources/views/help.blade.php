<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.help.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">
@include('includes.navbar')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body text-color">
                    <h1 class="card-title text-center mb-5 display-5 fw-bold">{{ __('messages.help.text') }}</h1>
                    <div class="mb-5">
                        <h3 class="mb-4 text-lg-center">{{ __('messages.help.faq_title') }}</h3>
                        <div class="accordion" id="faqAccordion">
                            @foreach ([
                                 ['title' => __('messages.help.faq_1_title') , 'content' => __('messages.help.faq_1_text')],
                                 ['title' =>__('messages.help.faq_2_title'), 'content' => __('messages.help.faq_2_text')],
                                 ['title' =>__('messages.help.faq_3_title'), 'content' => __('messages.help.faq_3_text')],
                                 ['title' =>__('messages.help.faq_4_title'), 'content' => __('messages.help.faq_4_text')],
                                 ['title' =>__('messages.help.faq_5_title'), 'content' => __('messages.help.faq_5_text')],
                                 ['title' =>__('messages.help.faq_6_title'), 'content' => __('messages.help.faq_6_text')],
                                 ['title' =>__('messages.help.faq_7_title'), 'content' => __('messages.help.faq_7_text')],
                                 ['title' =>__('messages.help.faq_8_title'), 'content' => __('messages.help.faq_8_text')],
                             ] as $index => $faq)
                                <div class="accordion-item text-color border-0 mb-3 rounded-3 shadow-sm">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button text-color collapsed card-header" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}"
                                                aria-expanded="false"
                                                aria-controls="collapse{{ $index }}">
                                            {{ $faq['title'] }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                         aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {!! $faq['content'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-center mt-5">
                        <h3 class="mb-3">{{ __('messages.help.needHelp') }}</h3>
                        <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="btn btn-gradient border-0 px-4 py-2 fw-semibold text-color_2">
                            {{ __('messages.help.email') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
</body>
</html>
