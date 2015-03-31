<?php
namespace Automation\Test\TestCase\Model\Behavior;

use Automation\Model\Behavior\AutomationBehavior;
use Cake\TestSuite\TestCase;

/**
 * Automation\Model\Behavior\AutomationBehavior Test Case
 */
class AutomationBehaviorTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Automation = new AutomationBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Automation);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
