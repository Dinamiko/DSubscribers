<?php

namespace Dinamiko\Dsubscribers\Tests\Integration;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase {
	protected $postIds = [];

	public function tearDown(): void {
		foreach ($this->postIds as $id) {
			wp_delete_post($id);
		}

		parent::tearDown();
	}

	public function testSample() {
		$post = wp_insert_post( [ 'post_title' => 'test' ] );
		$this->postIds[] = $post;

		$this->assertTrue( true );
	}
}
