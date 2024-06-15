<?php

/**
 * @file tests/jobs/notifications/OpenAccessMailUsersTest.php
 *
 * Copyright (c) 2024 Simon Fraser University
 * Copyright (c) 2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @brief Tests for open access mail to users job.
 */

namespace APP\tests\jobs\notifications;

use APP\issue\Repository as IssueRepository;
use APP\jobs\notifications\OpenAccessMailUsers;
use Mockery;
use PKP\db\DAORegistry;
use PKP\emailTemplate\Repository as EmailTemplateRepository;
use PKP\tests\PKPTestCase;

/**
 * @runTestsInSeparateProcesses
 *
 * @see https://docs.phpunit.de/en/9.6/annotations.html#runtestsinseparateprocesses
 */
class OpenAccessMailUsersTest extends PKPTestCase
{
    /**
     * base64_encoded serializion from OJS 3.4.0
     */
    protected string $serializedJobData = 'Tzo0MjoiQVBQXGpvYnNcbm90aWZpY2F0aW9uc1xPcGVuQWNjZXNzTWFpbFVzZXJzIjo2OntzOjEwOiIAKgB1c2VySWRzIjtPOjI5OiJJbGx1bWluYXRlXFN1cHBvcnRcQ29sbGVjdGlvbiI6Mjp7czo4OiIAKgBpdGVtcyI7YToyOntpOjA7aToxO2k6MTtpOjI7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9czoxMjoiACoAY29udGV4dElkIjtpOjE7czoxMDoiACoAaXNzdWVJZCI7aToxO3M6MTA6ImNvbm5lY3Rpb24iO3M6ODoiZGF0YWJhc2UiO3M6NToicXVldWUiO3M6NToicXVldWUiO3M6NzoiYmF0Y2hJZCI7czozNjoiOWMxYzQ1MDItNTI2MS00YjRhLTk2NWMtMjU2Y2QwZWFhYWE0Ijt9';

    /**
     * Test job is a proper instance
     */
    public function testUnserializationGetProperDepositIssueJobInstance(): void
    {
        $this->assertInstanceOf(
            OpenAccessMailUsers::class,
            unserialize(base64_decode($this->serializedJobData))
        );
    }

    /**
     * Ensure that a serialized job can be unserialized and executed
     */
    public function testRunSerializedJob()
    {
        $this->mockMail();

        // need to mock request so that a valid context information is set and can be retrived
        $this->mockRequest();

        /** @var OpenAccessMailUsers $openAccessMailUsersJob */
        $openAccessMailUsersJob = unserialize(base64_decode($this->serializedJobData));

        $journalDAOMock = Mockery::mock(\APP\journal\JournalDAO::class)
            ->makePartial()
            ->shouldReceive('getId')
            ->withAnyArgs()
            ->andReturn(
                Mockery::mock(\APP\journal\Journal::class)
                    ->makePartial()
                    ->shouldReceive([
                        'getData' => '',
                        'getPrimaryLocale' => 'en'
                    ])
                    ->withAnyArgs()
                    ->getMock()
            )
            ->getMock();

        DAORegistry::registerDAO('JournalDAO', $journalDAOMock);

        $issueRepoMock = Mockery::mock(app(IssueRepository::class))
            ->makePartial()
            ->shouldReceive([
                'get' => new \APP\issue\Issue(),
            ])
            ->withAnyArgs()
            ->getMock();

        app()->instance(IssueRepository::class, $issueRepoMock);

        $emailTemplateMock = Mockery::mock(\PKP\emailTemplate\EmailTemplate::class)
            ->makePartial()
            ->shouldReceive([
                'getLocalizedData' => 'some test string',
            ])
            ->withAnyArgs()
            ->getMock();

        $emailTemplateRepoMock = Mockery::mock(app(EmailTemplateRepository::class))
            ->makePartial()
            ->shouldReceive([
                'getByKey' => $emailTemplateMock,
            ])
            ->withAnyArgs()
            ->getMock();

        app()->instance(EmailTemplateRepository::class, $emailTemplateRepoMock);

        $this->assertNull($openAccessMailUsersJob->handle());
    }
}
