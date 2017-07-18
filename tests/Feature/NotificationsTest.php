<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class NotificationsTest extends TestCase
{
	use DatabaseMigrations;
    
    public function setUp()
    {
    	parent::setUp();

    	$this->signIn();
    }

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_reply_that_is_not_the_current_user()
    {
    	$thread = create('App\Thread')->subscribe();
		
		$this->assertCount(0, auth()->user()->notifications);

		$thread->addReply([
			'user_id' => auth()->id(),
			'body' => 'some reply here'
		]);

		$this->assertCount(0, auth()->user()->fresh()->notifications);

		$thread->addReply([
			'user_id' => create('App\User')->id,
			'body' => 'Some reply here'
		]);

		$this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    function a_user_can_fetch_their_unread_notifications()
    {
        create(DatabaseNotification::class);
        $this->assertCount(
            1,
            $this->getJson("/profiles/" . auth()->user()->name . "/notifications")->json()
        );
    }

    /** @test */
    function a_user_can_mark_their_notifications_as_read()
    {
		$thread = create('App\Thread')->subscribe();

    	$thread->addReply([
			'user_id' => create('App\User')->id,
			'body' => 'Some reply here'
		]);

		$user = auth()->user();

		$this->assertCount(1, $user->unreadNotifications);

		$notificationId = $user->unreadNotifications->first()->id;

		$this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

		$this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
