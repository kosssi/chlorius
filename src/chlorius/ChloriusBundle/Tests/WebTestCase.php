<?php

namespace chlorius\ChloriusBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is the base test case for all functional tests.
 * It bootstraps the database before each test class.
 *
 * @author Simon Constans <kosssi@gmail.com>
 */
class WebTestCase extends BaseWebTestCase
{
    const USER_EMAIL = 'demo@example.org';
    const USER_PASS = 'demodemo';
    const ADMIN_EMAIL = 'admin@example.org';
    const ADMIN_PASS = 'adminadmin';
    const DISABLED_USERNAME = 'nonactif@example.org';

    /** @var Application */
    private static $application;

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new \Symfony\Component\Console\Input\StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected static function executeSQL()
    {
        self::runCommand('propel:fixtures:load @NosBelIdeesUserBundle --yml');
    }

    protected static function loadUser()
    {
        //DELETE FROM fos_user;
        // php app/console doctrine:schema:drop
        // php app/console doctrine:schema:create
        // php app/console doctrine:fixtures:load --fixtures src/chlorius/ChloriusBundle/DataFixtures/ORM/ --append --purge-with-truncate

        self::runCommand('doctrine:schema:drop --force');
        self::runCommand('doctrine:schema:create');
        self::runCommand('doctrine:fixtures:load --append --fixtures src/chlorius/ChloriusBundle/DataFixtures/ORM/');
    }

    protected static function loadVoucher()
    {
        self::runCommand('propel:fixtures:load @NosBelIdeesVoucherBundle --yml');
    }

    /**
     * @param $username
     * @param $password
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function login($username, $password, Client $client = null)
    {
        if (!$client) {
            $client = static::createClient();
        }
        $client->request('GET', '/login');
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit(
            $form,
            array(
                '_username' => $username,
                '_password' => $password
            )
        );
        $client->followRedirect();

        return $client;
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function loginRoleUser(Client $client = null)
    {
        return $this->login(self::USER_EMAIL, self::USER_PASS, $client);
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function loginRoleAdmin(Client $client = null)
    {
        return $this->login(self::ADMIN_EMAIL, self::ADMIN_PASS, $client);
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @return WebTestCase
     */
    protected function logout(Client $client)
    {
        $client->request('GET', '/logout');
        $client->followRedirect();

        return $this;
    }

    /**
     * Asserts that client's response is of given status code.
     * If not it traverses the response to find symfony error and display it.
     *
     * @param integer $statusCode
     * @param Client $client
     */
    protected function assertStatusCode($statusCode, Client $client)
    {
        /** @var $response Response */
        $response = $client->getResponse();
        if ($response->isServerError() && $response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            $this->assertEquals(
                $statusCode,
                $response->getStatusCode(),
                $client->getCrawler()->filter('.block_exception h1')->text()
            );
        } else {
            $this->assertEquals($statusCode, $response->getStatusCode());
        }
    }

    protected function assertSEOCompatible(Client $client, $type = 'article')
    {
        $crawler = $client->getCrawler();
        $url = $client->getRequest()->getUri();
        $defaultTitle = ' - Nos Bel Idées';
        $title = str_replace($defaultTitle, '', $crawler->filter('title')->text());

        // title
        $this->assertNotEmpty($title, 'The title is empty.');
        // meta
        $this->assertCount(1, $crawler->filter('meta[charset=UTF-8]'));
        $this->assertCount(1, $crawler->filter('meta[property="og:title"][content="' . $title . '"]'));
        $this->assertCount(1, $crawler->filter('meta[property="og:type"][content="' . $type . '"]'));
        $this->assertCount(1, $crawler->filter('meta[property="og:url"][content="' . $url . '"]'));
        // img
        // $this->assertCount(0, $crawler->filter('img[alt=""]'));
        $this->assertCount(0, $crawler->filter('img:not([alt])'));
    }

    protected function assertLink($client, $link, $statusCode, $url)
    {
        $this->assertGreaterThan(0, count($link));
        $client->click($link->link());
        $this->assertUrl($url, $client, $statusCode);
    }

    protected function assertUrl($url, $client, $statusCode = 200)
    {
        $this->assertStatusCode($statusCode, $client);
        $this->assertEquals($url, $client->getRequest()->getRequestUri());
    }
}
