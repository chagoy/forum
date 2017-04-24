@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#"> 
                    <a href="/profiles/{{ $thread->creator->name }}"/>{{ $thread->creator->name }} posted: 
                    </a>
                    {{ $thread->title }}</div>

                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
            @foreach ($replies as $reply)
                @include ('threads.reply')
            @endforeach
            
            {{ $replies->links() }}

                @if (auth()->check())
            <form method="POST" action="{{ $thread->path() . '/replies/' }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="body" id="body" class="form-control" rows="5" placeholder="Leave a reply"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default">Post</button>
                </form>
            @else
                <p class="text-center"><a href="{{ route('login') }}">Login</a> to leave a post.</p>
            @endif
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p>
                        This thread was published at {{ $thread->created_at->diffForHumans() }} by <a href="#">{{ $thread->creator->name }}</a> and currently has {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
