<?php

/**
 * @file tests/jobs/statistics/CompileUniqueInvestigationsTest.php
 *
 * Copyright (c) 2024 Simon Fraser University
 * Copyright (c) 2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Tests for compile unique investigations job.
 */

namespace APP\tests\jobs\statistics;

use APP\jobs\statistics\CompileUniqueInvestigations;
use Mockery;
use PKP\db\DAORegistry;
use PKP\tests\PKPTestCase;

/**
 * @runTestsInSeparateProcesses
 *
 * @see https://docs.phpunit.de/en/9.6/annotations.html#runtestsinseparateprocesses
 */
class CompileUniqueInvestigationsTest extends PKPTestCase
{
    /**
     * base64_encoded serializion from OJS 3.4.0
     */
    protected string $serializedJobData = 'Tzo0NzoiQVBQXGpvYnNcc3RhdGlzdGljc1xDb21waWxlVW5pcXVlSW52ZXN0aWdhdGlvbnMiOjM6e3M6OToiACoAbG9hZElkIjtzOjI1OiJ1c2FnZV9ldmVudHNfMjAyNDAxMzAubG9nIjtzOjEwOiJjb25uZWN0aW9uIjtzOjg6ImRhdGFiYXNlIjtzOjU6InF1ZXVlIjtzOjU6InF1ZXVlIjt9';

    /**
     * Test job is a proper instance
     */
    public function testUnserializationGetProperDepositIssueJobInstance(): void
    {
        $this->assertInstanceOf(
            CompileUniqueInvestigations::class,
            unserialize(base64_decode($this->serializedJobData))
        );
    }

    /**
     * Ensure that a serialized job can be unserialized and executed
     */
    public function testRunSerializedJob()
    {
        /** @var CompileUniqueInvestigations $compileUniqueInvestigationsJob */
        $compileUniqueInvestigationsJob = unserialize(base64_decode($this->serializedJobData));

        $temporaryItemInvestigationsDAOMock = Mockery::mock(\APP\statistics\TemporaryItemInvestigationsDAO::class)
            ->makePartial()
            ->shouldReceive([
                'compileUniqueClicks' => null,
            ])
            ->withAnyArgs()
            ->getMock();

        DAORegistry::registerDAO('TemporaryItemInvestigationsDAO', $temporaryItemInvestigationsDAOMock);

        $this->assertNull($compileUniqueInvestigationsJob->handle());
    }
}
