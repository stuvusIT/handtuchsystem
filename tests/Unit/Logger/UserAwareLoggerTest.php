<?php

namespace Handtuchsystem\Test\Unit\Logger;

use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Logger\UserAwareLogger;
use Handtuchsystem\Models\LogEntry;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LogLevel;

class UserAwareLoggerTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Logger\UserAwareLogger::log
     * @covers \Handtuchsystem\Logger\UserAwareLogger::setAuth
     */
    public function testLog()
    {
        $user = (new User())->forceFill(['id' => 1, 'name' => 'admin']);

        /** @var LogEntry|MockObject $logEntry */
        $logEntry = $this->getMockBuilder(LogEntry::class)
            ->addMethods(['create'])
            ->getMock();
        $logEntry->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [['level' => LogLevel::INFO, 'message' => 'Some more informational foo']],
                [['level' => LogLevel::INFO, 'message' => 'admin (1): Some even more informational bar']]
            );

        /** @var Authenticator|MockObject $auth */
        $auth = $this->createMock(Authenticator::class);
        $auth->expects($this->exactly(2))
            ->method('user')
            ->willReturnOnConsecutiveCalls(
                null,
                $user
            );

        $logger = new UserAwareLogger($logEntry);
        $logger->setAuth($auth);

        $logger->log(LogLevel::INFO, 'Some more informational foo');
        $logger->log(LogLevel::INFO, 'Some even more informational bar');
    }
}
