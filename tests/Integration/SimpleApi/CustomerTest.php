<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Integration\SimpleApi;

use Gamez\Mite\Tests\Integration\SimpleApiTestCase;

/**
 * @coversDefaultClass  \Gamez\Mite\SimpleApi
 *
 * @internal
 *
 * @covers \Gamez\Mite\Api\HttpApiClient
 * @covers \Gamez\Mite\SimpleApi
 */
final class CustomerTest extends SimpleApiTestCase
{
    /**
     * @var array<non-empty-string, mixed>
     */
    private static array $customer;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$customer = self::$api->createCustomer([
            'name' => (new \ReflectionClass(self::class))->getShortName(),
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$api->deleteCustomer(self::$customer['id']);
    }

    /**
     * @covers ::createCustomer
     * @covers ::deleteCustomer
     *
     * @test
     */
    public function it_can_be_created_and_deleted(): void
    {
        $customer = self::$api->createCustomer(['name' => __FUNCTION__]);

        $this->assertCustomerStructure($customer);
        self::assertSame(__FUNCTION__, $customer['name']);

        self::$api->deleteCustomer($customer['id']);
    }

    /**
     * @covers ::getCustomer
     *
     * @test
     */
    public function it_can_be_fetched(): void
    {
        $customer = self::$api->getCustomer(self::$customer['id']);

        $this->assertCustomerStructure($customer);
        self::assertSame(self::$customer['id'], $customer['id']);
    }

    /**
     * @covers ::updateCustomer
     *
     * @test
     */
    public function it_can_be_updated(): void
    {
        $updated = self::$api->updateCustomer(self::$customer['id'], ['note' => __FUNCTION__]);

        $this->assertCustomerStructure($updated);
        self::assertSame(__FUNCTION__, $updated['note']);
    }

    /**
     * @covers ::getActiveCustomers
     *
     * @test
     */
    public function it_provides_a_list_of_active_customers(): void
    {
        self::$api->updateCustomer(self::$customer['id'], ['archived' => false]);

        $customers = self::$api->getActiveCustomers();

        self::assertGreaterThanOrEqual(1, $customers);

        $customerIds = array_column($customers, 'id');

        self::assertContains(self::$customer['id'], $customerIds);
    }

    /**
     * @covers ::getArchivedCustomers
     *
     * @test
     */
    public function it_provides_a_list_of_archived_customers(): void
    {
        self::$api->updateCustomer(self::$customer['id'], ['archived' => true]);

        $customers = self::$api->getArchivedCustomers();

        self::assertGreaterThanOrEqual(1, $customers);

        $customerIds = array_column($customers, 'id');

        self::assertContains(self::$customer['id'], $customerIds);
    }
}
