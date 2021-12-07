@extends('layouts.default')
@section('title')
{{ __('Ask :')  }}
<a href="{{ route('questions.create') }}" class="btn btn-outline-primary btn-bg">{{ __('What Is Your Question ?') }}</a>
@endsection

@section('content')

<x-alert />
@auth
    @else
    <div class="container  shadow  bg-body rounded">
        <div class="row">
            <div class="col-6 align-self-center">
                <div class="jumbotron my-4">
                    <h1>Welcome ,<br> The Social Q&A Community</h1>
                    <p class="lead lh-lg">The question and answer site designed to help people, to help each other: To ask, to learn, to share, to grow.</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">Register</a> OR
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">{{ __('Login') }}</a>
                </div>
            </div>
            <div class="col-6 p-0">
                <img src="images\qa.jpg" class="img-fluid" alt="home">
            </div>
         </div>
    </div>
@endauth


        <h2 class="p-2 shadow  bg-body rounded mt-2">{{ __('Questions') }} & Answers</h2>

@foreach($questions as $question)
<div class="card mb-3 shadow  bg-body rounded">
    <div class="card-body">
        <h5 class="card-title"><a href="{{ route('questions.show', $question->id) }}" class="btn btn-outline-primary">{{ $question->title }} ?</a></h5>
        <div class="text-muted mb-4">
            @lang('Asked'): {{ $question->created_at->diffForHumans() }},
            {{ trans('By') }}: {{ $question->user->name }},
            {{ __('Answers') }}: {{ $question->answers_count }}
        </div>
        <p class="card-text">{{ Str::words($question->description, 30) }}</p>
        <div >Tags: {{ implode(', ', $question->tags->pluck('name')->toArray()) }}</div>
    </div>
    @can('update', $question)
    <div class="card-footer">
        <div class="d-flex justify-content-start">
            <div>
                <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm  btn-outline-success">Edit</a>
            </div> <span> __ </span>
            <form action="{{ route('questions.destroy', $question->id) }}" method="post">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    </div>
    @endcan
</div>
@endforeach

{{ $questions->withQueryString()->links() }}

@endsection

