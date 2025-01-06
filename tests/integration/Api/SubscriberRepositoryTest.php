<?php
declare( strict_types=1 );

namespace Dinamiko\Dsubscribers\Tests\Integration\Api;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;
use Exception;
use PHPUnit\Framework\TestCase;

class SubscriberRepositoryTest extends TestCase {

	private SubscriberRepository $repository;
	private string $email;

	public function setUp(): void {
		parent::setUp();

		$this->repository = new SubscriberRepository();
		$this->email      = 'test@example.com';
	}

	public function tearDown(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';
		$wpdb->query( "TRUNCATE TABLE $table_name" );

		parent::tearDown();
	}

	public function test_subscribe() {
		$this->repository->subscribe( $this->email );

		$subscriber = $this->repository->subscriber( $this->email );
		$this->assertTrue( $subscriber['email'] === $this->email );
	}

	public function test_unsubscribe_email_exists() {
		$this->expectException( Exception::class );

		$this->repository->subscribe( $this->email );
		$this->repository->subscribe( $this->email );
	}

	public function test_unsubscribe() {
		$this->repository->subscribe( $this->email );
		$this->repository->unsubscribe( $this->email );

		$subscriber = $this->repository->subscriber( $this->email );
		$this->assertNull( $subscriber );
	}

	public function test_unsubscribe_email_not_exists() {
		$this->expectException( Exception::class );

		$this->repository->unsubscribe( $this->email );
	}

	public function test_subscribers() {
		$this->assertEquals( 0, count( $this->repository->subscribers() ) );

		$this->repository->subscribe( $this->email );
		$this->assertEquals( 1, count( $this->repository->subscribers() ) );
	}

	public function test_subscribers_args() {
		for ( $i = 0; $i < 5; $i ++ ) {
			$this->repository->subscribe( "test{$i}@example.com}" );
		}

		$args = [ 'limit' => 2, ];

		$this->assertEquals( 2, count( $this->repository->subscribers( $args ) ) );
	}

	public function test_subscribers_by_email() {
		$this->repository->subscribe( $this->email );

		$subscribers = $this->repository->subscribers( [], $this->email );

		$this->assertEquals(
			$this->email,
			$subscribers[0]->email
		);
	}

	public function test_suscribers_update() {
		$this->repository->subscribe( $this->email );
		$newEmail = 'new@example.com';

		$this->repository->update(
			$this->email,
			$newEmail
		);

		$subscriber = $this->repository->subscriber( $newEmail );
		$this->assertTrue( $subscriber['email'] === $newEmail );
	}
}
