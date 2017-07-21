<?php

namespace App;

use App\Events\ThreadhasNewReply;
use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

	protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

	protected static function boot()
    {
        parent::boot();

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        $this->notifySubscribers($reply);

        return $reply;
    }

    public function scopeFilter($query, Threadfilters $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null) 
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function hasUpdatesFor($user)
    {
        //look in the cache for the proper key
        $key = $user->visitedThreadCacheKey($this);
        //compare that carbon instance with last updated at time of thread
        //returns a boolean now to determine if bold or not on the view
        return $this->updated_at > cache($key);
    }

    public function notifySubscribers($reply)
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each->notify($reply);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }
}
