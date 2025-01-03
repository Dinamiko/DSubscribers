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
		$this->expectException(Exception::class);

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
		$this->expectException(Exception::class);

		$this->repository->unsubscribe( $this->email );
	}
}
