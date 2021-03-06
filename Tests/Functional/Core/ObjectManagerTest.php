<?php

namespace Cundd\Rest\Tests\Functional\Core;

use Cundd\Rest\Authentication\AuthenticationProviderInterface;
use Cundd\Rest\Configuration\TypoScriptConfigurationProvider;
use Cundd\Rest\DataProvider\DataProvider;
use Cundd\Rest\DataProvider\DataProviderInterface;
use Cundd\Rest\DataProvider\VirtualObjectDataProvider;
use Cundd\Rest\Handler\CrudHandler;
use Cundd\Rest\Handler\HandlerInterface;
use Cundd\Rest\ObjectManager;
use Cundd\Rest\RequestFactory;
use Cundd\Rest\RequestFactoryInterface;
use Cundd\Rest\ResponseFactory;
use Cundd\Rest\ResponseFactoryInterface;
use Cundd\Rest\Tests\Functional\AbstractCase;

class ObjectManagerTest extends AbstractCase
{
    /**
     * @var ObjectManager
     */
    protected $fixture;

    public function setUp()
    {
        parent::setUp();
        require_once __DIR__ . '/../../FixtureClasses.php';
        $this->fixture = new ObjectManager();
    }

    public function tearDown()
    {
        // Reset the last request
        if ($this->fixture) {
            $this->fixture->getRequestFactory()->resetRequest();
        }
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getRequestFactoryTest()
    {
        $object = $this->fixture->getRequestFactory();
        $this->assertInstanceOf(RequestFactoryInterface::class, $object);
        $this->assertInstanceOf(RequestFactory::class, $object);
    }

    /**
     * @test
     */
    public function getResponseFactoryTest()
    {
        $object = $this->fixture->getResponseFactory();
        $this->assertInstanceOf(ResponseFactoryInterface::class, $object);
        $this->assertInstanceOf(ResponseFactory::class, $object);
    }

    /**
     * @test
     */
    public function getConfigurationProviderTest()
    {
        $object = $this->fixture->getConfigurationProvider();
        $this->assertInstanceOf(TypoScriptConfigurationProvider::class, $object);
    }

    /**
     * @test
     */
    public function getAuthenticationProviderTest()
    {
        $object = $this->fixture->getAuthenticationProvider();
        $this->assertInstanceOf(AuthenticationProviderInterface::class, $object);
    }

    /**
     * @test
     * @dataProvider dataProviderTestGenerator
     * @param string $url
     * @param string $expectedClass
     * @param array  $classToBuild
     * @throws \Exception
     */
    public function getDataProviderTest($url, $expectedClass, $classToBuild = [])
    {
        $_GET['u'] = $url;
        if ($classToBuild) {
            $this->buildClass($classToBuild);
        }

        $dataProvider = $this->fixture->getDataProvider();
        $this->assertInstanceOf($expectedClass, $dataProvider);
        $this->assertInstanceOf(DataProviderInterface::class, $dataProvider);
        $this->assertInstanceOf(DataProvider::class, $dataProvider);
    }

    public function dataProviderTestGenerator()
    {
        $defaultDataProvider = DataProvider::class;

        return [
            //     url,                expected,                     classToBuild
            [
                '',
                DataProvider::class,
                [],
            ],
            [
                'my_ext-my_model/1',
                'Tx_MyExt_Rest_DataProvider',
                ['Tx_MyExt_Rest_DataProvider', '', $defaultDataProvider],
            ],
            [
                'my_ext-my_model/1.json',
                'Tx_MyExt_Rest_DataProvider',
                ['Tx_MyExt_Rest_DataProvider', '', $defaultDataProvider],
            ],
            [
                'MyExt-MyModel/1',
                'Tx_MyExt_Rest_DataProvider',
                ['Tx_MyExt_Rest_DataProvider', '', $defaultDataProvider],
            ],
            [
                'MyExt-MyModel/1.json',
                'Tx_MyExt_Rest_DataProvider',
                ['Tx_MyExt_Rest_DataProvider', '', $defaultDataProvider],
            ],
            [
                'vendor-my_second_ext-my_model/1',
                '\\Vendor\\MySecondExt\\Rest\\DataProvider',
                ['DataProvider', 'Vendor\\MySecondExt\\Rest', $defaultDataProvider],
            ],
            [
                'Vendor-MySecondExt-MyModel/1',
                '\\Vendor\\MySecondExt\\Rest\\DataProvider',
                ['DataProvider', 'Vendor\\MySecondExt\\Rest', $defaultDataProvider],
            ],
            [
                'Vendor-NotExistingExt-MyModel/1',
                $defaultDataProvider,
            ],
            [
                'Vendor-NotExistingExt-MyModel/1.json',
                $defaultDataProvider,
            ],
            [
                'MyThirdExt-MyModel/1.json',
                'Tx_MyThirdExt_Rest_MyModelDataProvider',
                ['Tx_MyThirdExt_Rest_MyModelDataProvider', '', $defaultDataProvider],
            ],
            [
                'Vendor-MySecondExt-MyModel/1.json',
                '\\Vendor\\MySecondExt\\Rest\\MyModelDataProvider',
                ['MyModelDataProvider', 'Vendor\\MySecondExt\\Rest', $defaultDataProvider],
            ],
            [
                'virtual_object-page',
                VirtualObjectDataProvider::class,
            ],
            [
                'virtual_object-page.json',
                VirtualObjectDataProvider::class,
            ],
            [
                'virtual_object-page/1',
                VirtualObjectDataProvider::class,
            ],
            [
                'virtual_object-page/1.json',
                VirtualObjectDataProvider::class,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider handlerTestGenerator
     * @param string $url
     * @param string $expectedClass
     * @param array  $classToBuild
     * @throws \Exception
     */
    public function getHandlerTest($url, $expectedClass, $classToBuild = [])
    {
        $_GET['u'] = $url;
        if ($classToBuild) {
            $this->buildClass($classToBuild);
        }

        $handler = $this->fixture->getHandler();
        $this->assertInstanceOf($expectedClass, $handler);
        $this->assertInstanceOf(HandlerInterface::class, $handler);
        $this->assertInstanceOf(CrudHandler::class, $handler);
    }

    public function handlerTestGenerator()
    {
        $defaultHandler = CrudHandler::class;

        return [
            // URL,
            // Expected result class,
            // Class to Build
            [
                'my_ext-my_model/1',
                'Tx_MyExt_Rest_Handler',
                ['Tx_MyExt_Rest_Handler', '', $defaultHandler],
            ],
            [
                'my_ext-my_model/1.json',
                'Tx_MyExt_Rest_Handler',
                ['Tx_MyExt_Rest_Handler', '', $defaultHandler],
            ],
            [
                'MyExt-MyModel/1',
                'Tx_MyExt_Rest_Handler',
                ['Tx_MyExt_Rest_Handler', '', $defaultHandler],
            ],
            [
                'MyExt-MyModel/1.json',
                'Tx_MyExt_Rest_Handler',
                ['Tx_MyExt_Rest_Handler', '', $defaultHandler],
            ],
            [
                'vendor-my_second_ext-my_model/1',
                '\\Vendor\\MySecondExt\\Rest\\Handler',
                ['Handler', 'Vendor\\MySecondExt\\Rest\\', $defaultHandler],
            ],
            [
                'Vendor-MySecondExt-MyModel/1',
                '\\Vendor\\MySecondExt\\Rest\\Handler',
                ['Handler', 'Vendor\\MySecondExt\\Rest\\', $defaultHandler],
            ],
            [
                'Vendor-MySecondExt-WhatEver/1',
                '\\Vendor\\MySecondExt\\Rest\\Handler',
                ['Handler', 'Vendor\\MySecondExt\\Rest\\', $defaultHandler],
            ],
            [
                'Vendor-MySecondExt-WhatEver/',
                '\\Vendor\\MySecondExt\\Rest\\Handler',
                ['Handler', 'Vendor\\MySecondExt\\Rest\\', $defaultHandler],
            ],
            [
                'Vendor-MySecondExt-WhatEver',
                '\\Vendor\\MySecondExt\\Rest\\Handler',
                ['Handler', 'Vendor\\MySecondExt\\Rest\\', $defaultHandler],
            ],
            [
                'Vendor-NotExistingExt-MyModel/1',
                $defaultHandler,
            ],
            [
                'Vendor-NotExistingExt-MyModel/1.json',
                $defaultHandler,
            ],
        ];
    }
}
