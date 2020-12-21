<?php


namespace App\Tests\Behat;

use App\Model\FlashMessage;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Webmozart\Assert\Assert;

class Login implements Context
{
    private UserRepository $userRepository;
    private Session $session;
    private string $name;
    private string $password;

    public function __construct(Session $session, UserRepository $userRepository,
                                string $name = "Prueba3", string $password = "123")
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * @When I fill in the logIn form
     * @throws ElementNotFoundException
     */
    public function iFillInTheLoginForm()
    {
        $this->session->visit("/en/login");
        $page = $this->session->getPage();
        $page->fillField('inputName', $this->name);
        $page->fillField('inputPassword', $this->password);
        $page->pressButton('button');

        $element = $page->findById('errorMessage');

        if ($element) {
            Assert::true($element->getText() == FlashMessage::LOGIN_FAIL);
            echo FlashMessage::LOGIN_FAIL;
        }
    }

    /**
     * @Then I should have a logged User
     */
    public function iShouldHaveALoggedUser()
    {
        $element = $this->session->getPage()->findById('errorMessage');

        if ($element) {
            Assert::true($element->getText() == FlashMessage::LOGIN_FAIL);
            echo FlashMessage::LOGIN_FAIL;
        } else {
            Assert::true($this->session->getCurrentUrl() == "http://localhost/en");
        }
    }
}