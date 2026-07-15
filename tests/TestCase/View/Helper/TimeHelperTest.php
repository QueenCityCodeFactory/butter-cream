<?php
declare(strict_types=1);

namespace ButterCream\Test\TestCase\View\Helper;

use ArrayObject;
use ButterCream\View\Helper\TimeHelper;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use DateTimeImmutable;
use DateTimeZone;

/**
 * ButterCream\View\Helper\TimeHelper Test Case
 */
class TimeHelperTest extends TestCase
{
    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        DateTime::setTestNow();
        parent::tearDown();
    }

    /**
     * Explicit timezones take precedence over every configured context.
     *
     * @return void
     */
    public function testUserFormatExplicitTimezoneTakesPrecedence(): void
    {
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('timezone', 'America/Chicago')
            ->withAttribute('identity', new ArrayObject(['timezone' => 'America/Los_Angeles']));
        $request->getSession()->write('Auth.timezone', 'America/Denver');
        $helper = $this->createHelper($request, ['outputTimezone' => 'Europe/London']);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm', false, 'Asia/Tokyo');

        $this->assertSame('2026-07-15 21:00', $result);
    }

    /**
     * Helper outputTimezone config takes precedence over request context.
     *
     * @return void
     */
    public function testUserFormatUsesConfiguredOutputTimezone(): void
    {
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('timezone', 'America/Chicago');
        $helper = $this->createHelper($request, ['outputTimezone' => 'Europe/London']);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm');

        $this->assertSame('2026-07-15 13:00', $result);
    }

    /**
     * Request middleware can provide the resolved user timezone.
     *
     * @return void
     */
    public function testUserFormatUsesRequestTimezoneAttribute(): void
    {
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('timezone', 'America/New_York');
        $helper = $this->createHelper($request);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm');

        $this->assertSame('2026-07-15 08:00', $result);
    }

    /**
     * Identity timezone is used when middleware did not resolve one.
     *
     * @return void
     */
    public function testUserFormatUsesIdentityTimezone(): void
    {
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('identity', new ArrayObject(['timezone' => 'America/Los_Angeles']));
        $helper = $this->createHelper($request);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm');

        $this->assertSame('2026-07-15 05:00', $result);
    }

    /**
     * Invalid request context falls through to the next valid timezone.
     *
     * @return void
     */
    public function testUserFormatSkipsInvalidContextTimezone(): void
    {
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('timezone', 'Not/A_Timezone')
            ->withAttribute('identity', new ArrayObject(['timezone' => 'America/Los_Angeles']));
        $helper = $this->createHelper($request);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm');

        $this->assertSame('2026-07-15 05:00', $result);
    }

    /**
     * Legacy Auth.timezone sessions remain supported.
     *
     * @return void
     */
    public function testUserFormatUsesLegacySessionTimezone(): void
    {
        $request = new ServerRequest(['url' => '/']);
        $request->getSession()->write('Auth.timezone', 'America/Denver');
        $helper = $this->createHelper($request);

        $result = $helper->userFormat($this->utcDate(), 'yyyy-MM-dd HH:mm');

        $this->assertSame('2026-07-15 06:00', $result);
    }

    /**
     * Native DateTimeInterface values keep their original instant.
     *
     * @return void
     */
    public function testUserFormatPreservesNativeDateTimeInstant(): void
    {
        $date = new DateTimeImmutable('2026-07-15 09:00:00', new DateTimeZone('America/New_York'));
        $helper = $this->createHelper(new ServerRequest(['url' => '/']));

        $result = $helper->userFormat($date, 'yyyy-MM-dd HH:mm', false, 'UTC');

        $this->assertSame('2026-07-15 13:00', $result);
    }

    /**
     * Integer timestamps, including the Unix epoch, are supported.
     *
     * @return void
     */
    public function testUserFormatSupportsUnixEpoch(): void
    {
        $helper = $this->createHelper(new ServerRequest(['url' => '/']));

        $result = $helper->userFormat(0, 'yyyy-MM-dd HH:mm', false, 'UTC');

        $this->assertSame('1970-01-01 00:00', $result);
    }

    /**
     * Relative formatting keeps the instant from native datetime objects.
     *
     * @return void
     */
    public function testRelativeTimePreservesNativeDateTimeInstant(): void
    {
        DateTime::setTestNow(new DateTime('2026-07-15 14:00:00', new DateTimeZone('UTC')));
        $date = new DateTimeImmutable('2026-07-15 09:00:00', new DateTimeZone('America/New_York'));
        $helper = $this->createHelper(new ServerRequest(['url' => '/']));

        $this->assertSame('1 hour ago', $helper->relativeTime($date));
    }

    /**
     * Semantic output keeps an unambiguous original instant.
     *
     * @return void
     */
    public function testSemanticPreservesNativeDateTimeOffset(): void
    {
        $date = new DateTimeImmutable('2026-07-15 09:00:00', new DateTimeZone('America/New_York'));
        $request = (new ServerRequest(['url' => '/']))
            ->withAttribute('timezone', 'America/Los_Angeles');
        $helper = $this->createHelper($request);

        $result = $helper->semantic($date, 'yyyy-MM-dd HH:mm');

        $this->assertSame(
            '<time datetime="2026-07-15T09:00:00-04:00">2026-07-15 06:00</time>',
            $result,
        );
    }

    /**
     * Invalid dates continue to use the requested fallback.
     *
     * @return void
     */
    public function testUserFormatReturnsInvalidFallback(): void
    {
        $helper = $this->createHelper(new ServerRequest(['url' => '/']));

        $this->assertSame('Unavailable', $helper->userFormat('not-a-date', null, 'Unavailable'));
    }

    /**
     * Create a TimeHelper with the provided request and config.
     *
     * @param \Cake\Http\ServerRequest $request Server request.
     * @param array<string, mixed> $config Helper configuration.
     * @return \ButterCream\View\Helper\TimeHelper
     */
    private function createHelper(ServerRequest $request, array $config = []): TimeHelper
    {
        $response = new Response(['charset' => 'UTF-8']);
        $view = new View($request, $response);

        return new TimeHelper($view, $config);
    }

    /**
     * Return a stable UTC test value.
     *
     * @return \DateTimeImmutable
     */
    private function utcDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-07-15 12:00:00', new DateTimeZone('UTC'));
    }
}
