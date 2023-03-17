<?php
// tests/Controller/MailControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Annotation\Route;

class WebTestCaseController extends WebTestCase
{
    #[Route('/mail', name: 'app_mail')]
    public function testMailIsSentAndContentIsOk()
    {
        $client = static::createClient();
        $client->request('GET', '/mail/send');
        $this->assertResponseIsSuccessful();

        $this->assertEmailCount(1); // use assertQueuedEmailCount() when using Messenger

        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'Welcome');
        $this->assertEmailTextBodyContains($email, 'Welcome');
    }
}