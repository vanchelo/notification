<?php

use Mockery as m;

class NotificationTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testIsConstructed()
    {
        $notification = $this->getNotification();
        $this->assertEquals('default', $notification->getDefaultContainerName());
        $this->assertCount(0, $notification->getContainers());
    }

    public function testGetDefaultContainerName()
    {
        $notification = $this->getNotification();
        $this->assertEquals('default', $notification->getDefaultContainerName());
    }

    public function testSetContainerTypes()
    {
        $notification = $this->getNotification();
        $this->assertEquals(array(), $notification->getContainerTypes('default'));

        $notification->setContainerTypes('default', array('success', 'info'));
        $this->assertEquals(array('success', 'info'), $notification->getContainerTypes('default'));
    }

    public function testGetTypesContainerForContainer()
    {
        $notification = $this->getNotification();
        $this->assertEquals(array(), $notification->getContainerTypes('default'));
        $this->assertEquals(array(), $notification->getContainerTypes('test'));

        $notification->setContainerTypes('default', array('success', 'info'));
        $this->assertEquals(array('success', 'info'), $notification->getContainerTypes('default'));
        $this->assertEquals(array(), $notification->getContainerTypes('test'));
    }

    public function testSetContainerFormat()
    {
        $notification = $this->getNotification();
        $this->assertNull($notification->getContainerFormat('default'));

        $notification->setContainerFormat('default', ':message');
        $this->assertEquals(':message', $notification->getContainerFormat('default'));
    }

    public function testGetContainerFormatForContainer()
    {
        $notification = $this->getNotification();
        $this->assertNull($notification->getContainerFormat('default'));
        $this->assertNull($notification->getContainerFormat('test'));

        $notification->setContainerFormat('default', ':message');
        $this->assertEquals(':message', $notification->getContainerFormat('default'));
        $this->assertNull($notification->getContainerFormat('test'));
    }

    public function testSetContainerContainerFormats()
    {
        $notification = $this->getNotification();
        $this->assertEquals(array(), $notification->getContainerFormats('default'));

        $notification->setContainerFormats('default', array('info' => ':message'));
        $this->assertEquals(array('info' => ':message'), $notification->getContainerFormats('default'));
    }

    public function testGetContainerFormatsForContainer()
    {
        $notification = $this->getNotification();
        $this->assertEquals(array(), $notification->getContainerFormats('default'));
        $this->assertEquals(array(), $notification->getContainerFormats('test'));

        $notification->setContainerFormats('default', array('info' => ':message'));
        $this->assertEquals(array('info' => ':message'), $notification->getContainerFormats('default'));
        $this->assertEquals(array(), $notification->getContainerFormats('test'));
    }

    public function testAddContainer()
    {
        $notification = $this->getNotification();
        $this->assertCount(0, $notification->getContainers());

        $notification->addContainer('test');
        $this->assertCount(1, $notification->getContainers());
    }

    public function testAddExistingContainer()
    {
        $notification = $this->getNotification();
        $this->assertCount(0, $notification->getContainers());

        $notification->addContainer('test');
        $this->assertCount(1, $notification->getContainers());

        $notification->addContainer('test');
        $this->assertCount(1, $notification->getContainers());
    }

    public function testNotificationBagInstanceOnDefaultContainer()
    {
        $notification = $this->getNotification();
        $this->assertInstanceOf('Krucas\Notification\NotificationsBag', $notification->container());
    }

    public function testNotificationBagInstanceOnDefaultContainerUsingMagicMethod()
    {
        $notification = $this->getNotification();
        $this->assertEquals('default', $notification->getName());
    }

    public function testNotificationBagInstanceOnAddedContainer()
    {
        $notification = $this->getNotification();
        $notification->addContainer('test');
        $this->assertInstanceOf('Krucas\Notification\NotificationsBag', $notification->container('test'));
    }

    public function testNotificationBagInstanceOnNonExistingContainer()
    {
        $notification = $this->getNotification();
        $this->assertInstanceOf('Krucas\Notification\NotificationsBag', $notification->container('test'));
    }

    public function testNotificationBagInstanceOnNonExistingContainerWithResolvedParams()
    {
        $notification = $this->getNotification();
        $notification->setContainerTypes('test', array('success'));
        $notification->setContainerFormat('test', ':message');
        $notification->setContainerFormats('test', array('success' => ':message - OK'));
        $container = $notification->container('test');
        $this->assertInstanceOf('Krucas\Notification\NotificationsBag', $container);
        $this->assertEquals(array('success'), $container->getTypes());
        $this->assertEquals(':message', $container->getDefaultFormat());
        $this->assertEquals(':message - OK', $container->getFormat('success'));
    }

    public function testCallbackOnNotificationBag()
    {
        $notification = $this->getNotification();
        $this->assertEquals(array(), $notification->container('default')->getTypes());
        $notification->container('default', function ($container) {
            $container->addType('info');
        });
        $this->assertEquals(array('info'), $notification->container('default')->getTypes());
    }

    protected function getNotification()
    {
        return new \Krucas\Notification\Notification('default');
    }
}
