<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Model\FlashMessage;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class Register implements Context
{
    private KernelInterface $kernel;
    private UserRepository $userRepository;
    private Session $session;
    private string $name;
    private string $password;

    public function __construct(Session $session, KernelInterface $kernel, UserRepository $userRepository,
                                string $name = "Prueba3", string $password = "123")
    {
        $this->kernel = $kernel;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * @Given I am on :arg1
     * @param $route
     * @throws Exception
     */
    public function iAmOn($route)
    {
        $response = $this->kernel->handle(Request::create($route, 'GET'));
        Assert::notNull($response);
    }

    /**
     * @When I fill in the register form
     * @throws ElementNotFoundException
     */
    public function iFillInTheRegisterForm()
    {
        $this->session->visit("/en/register");
        $page = $this->session->getPage();
        $page->fillField('user_name', $this->name);
        $page->fillField('user_password', $this->password);
        $page->pressButton('user_register');

        $element = $page->findById('errorMessage');

        if ($element) {
            $this->error($element->getText());
        } else {
            Assert::true($this->session->getCurrentUrl() == "http://localhost/en/login/" . $this->name);
            Assert::true($page->findById('successMessage')->getText() == FlashMessage::REGISTER_OK);
        }
    }

    /**
     * @Then I should have a registered User
     */
    public function iShouldHaveARegisteredUser()
    {
        $element = $this->session->getPage()->findById('errorMessage');

        if ($element) {
            $this->error($element->getText());
        } else {
            $userCheck = $this->userRepository->findOneBy(['name' => $this->name, 'state' => 'active']);
            Assert::notNull($userCheck);
        }
    }

    public function error($element)
    {
        Assert::true($element == FlashMessage::REGISTER_FAIL
            || $element == FlashMessage::REGISTER_SPAM
            || $element == FlashMessage::REGISTER_FAIL_SPAM_CHECKER);

        switch ($element) {
            case FlashMessage::REGISTER_FAIL:
                echo FlashMessage::REGISTER_FAIL;
                break;
            case FlashMessage::REGISTER_SPAM:
                echo FlashMessage::REGISTER_SPAM;
                break;
            case FlashMessage::REGISTER_FAIL_SPAM_CHECKER:
                echo FlashMessage::REGISTER_FAIL_SPAM_CHECKER;
                break;
        }
    }
}
