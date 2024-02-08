<?php

namespace Tests\Unit\Services\SmsService\Providers;

use App\Models\User;
use App\Notifications\TransactionCompleteNotification;
use App\Services\SmsService\Providers\KavenegarSmsProvider;
use App\Services\SmsService\SmsServiceInterface;
use Illuminate\Notifications\Notification;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;
use Mockery;
use Tests\TestCase;

class KavenegarSmsProviderTest extends TestCase
{
    protected $kavenegarMock;
    protected $kavenegarSmsProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instance(SmsServiceInterface::class, Mockery::mock(SmsServiceInterface::class, function ($mock) {
        }));
        // Set necessary config here
        config()->set('sms_providers.kavenegar.line_number', 'Your_Line_Number');
        config()->set('sms_providers.kavenegar.api_key', 'Your_Api_Key');
        $this->kavenegarMock = Mockery::mock(KavenegarApi::class);
        $this->kavenegarSmsProvider = new KavenegarSmsProvider();
        $this->kavenegarSmsProvider->api = $this->kavenegarMock;
    }

    public function testSendSmsSuccessfully(): void
    {
        $user = new User();
        $user->phone_number = '+123456789';
        $notification = new TransactionCompleteNotification('sender', 1000);

        $this->kavenegarMock->shouldReceive('Send')
            ->once()
            ->andReturnTrue();

        $this->kavenegarSmsProvider->send($user, $notification);
        $this->assertTrue(true);
    }

    public function testApiExceptionThrown(): void
    {
        $user = new User();
        $user->phone_number = '+123456789';
        $notification = new TransactionCompleteNotification('sender', 1000);

        $this->kavenegarMock->shouldReceive('Send')
            ->once()
            ->andThrow(ApiException::class);

        $this->expectException(ApiException::class);
        $this->kavenegarSmsProvider->send($user, $notification);
    }

    public function testHttpExceptionThrown(): void
    {
        $user = new User();
        $user->phone_number = '+123456789';
        $notification = new TransactionCompleteNotification('sender', 1000);


        $this->kavenegarMock->shouldReceive('Send')
            ->once()
            ->andThrow(HttpException::class);

        $this->expectException(HttpException::class);
        $this->kavenegarSmsProvider->send($user, $notification);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
