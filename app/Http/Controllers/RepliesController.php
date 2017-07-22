<?php

namespace App\Http\Controllers;

use App\Http\Forms\CreatePostForm;
use App\Thread;
use App\Reply;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth', ['except' => 'index']);
	}

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }
    
    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
        return $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ])->load('owner');
    }

    public function update(Reply $reply)
    {
        try {
            $this->authorize('update', $reply);

            $this->validate(request(), ['body' => 'required|spamfree']);
            
            $reply->update(request(['body']));
        } catch (\Exception $e) {
            return response(
                'Sorry, your reply could not be saved', 422);
        }
        
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);
        
        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
