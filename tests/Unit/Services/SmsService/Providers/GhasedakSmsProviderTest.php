<?php

namespace Tests\Unit\Services\SmsService\Providers;

use App\Models\User;
use App\Notifications\TransactionCompleteNotification;
use App\Services\SmsService\Providers\GhasedakSmsProvider;
use App\Services\SmsService\SmsServiceInterface;
use Ghasedak\GhasedakApi;
use Illuminate\Notifications\Notification;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * This test class GhasedakSmsProviderTest is for the send method in the
 * GhasedakSmsProvider class.
 *
 * The send method in the GhasedakSmsProvider class is responsible for sending SMS using
 * the Ghasedak SMS Service. It requires a User instance and a Notification instance as input
 * and sends an SMS to the phone number of the user provided in the User instance.
 */
class GhasedakSmsProviderTest extends TestCase
{
    /** @var GhasedakSmsProvider */
    private $ghasedakSmsProvider;

    /** @var MockObject|GhasedakApi */
    private $ghasedakApiMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instance(SmsServiceInterface::class, Mockery::mock(SmsServiceInterface::class, function ($mock) {
        }));
        // Set necessary config here
        config()->set('sms_providers.ghasedak.line_number', 'Your_Line_Number');
        config()->set('sms_providers.ghasedak.api_key', 'Your_Api_Key');
        $this->ghasedakApiMock = $this->getMockBuilder(GhasedakApi::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ghasedakSmsProvider = new GhasedakSmsProvider();
        $this->ghasedakSmsProvider->api = $this->ghasedakApiMock;
    }

    /**
     * Test that the send method succeeds when provided valid inputs.
     */
    public function testSendWithValidInputs(): void
    {
        $user = new User();
        $user->phone_number = '+123456789';
        $notification = new TransactionCompleteNotification('sender', 1000);
        $this->ghasedakApiMock->expects($this->once())
            ->method('SendSimple')
            ->willReturn(true);

        $this->ghasedakSmsProvider->send($user, $notification);
    }

    /**
     * Testing when the send method is provided with an invalid phone number.
     */
    public function testSendWithInvalidPhoneNumber(): void
    {
        $user = new User();
        $user->phone_number = null;
        $notification = new TransactionCompleteNotification('sender', 1000);
        $this->ghasedakApiMock->expects($this->once())
            ->method('SendSimple')
            ->willReturn(false);
        $this->ghasedakSmsProvider->send($user, $notification);
    }

}
