<?php

/**
 * This file is part of O3-Shop Demo Data Installer.
 *
 * O3-Shop is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

namespace OxidEsales\DemoDataInstaller\Tests\Integration;

use org\bovigo\vfs\vfsStream;
use OxidEsales\DemoDataInstaller\DemoDataInstaller;
use OxidEsales\DemoDataInstaller\DemoDataPathSelector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

final class DemoDataInstallerTest extends TestCase
{
    private $temporaryPath;
    private $vendorPath;
    private $targetPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->temporaryPath = Path::join(__DIR__, '..', 'tmp');
        $this->vendorPath = Path::join(__DIR__, '..', 'tmp', 'testData');
        $this->targetPath = Path::join(__DIR__, '..', 'tmp', 'testTarget');
        $this->buildDirectory();
    }

    protected function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->temporaryPath);
        parent::tearDown();
    }

    public function testExecuteDemoDataInstaller(): void
    {
        $demoDataInstaller = $this->buildDemoDataInstaller();

        $this->assertSame(0, $demoDataInstaller->execute());
        $this->assertSame(4, $this->countFiles($this->targetPath));
    }

    private function buildDirectory(): void
    {
        $structure = [
            'oxid-esales' => [
                'oxideshop-demodata-ce' => [
                    'src' => [
                        'out' => [
                            'pictures' => [
                                'picture1' => 'picture 1',
                                'picture2' => 'picture 2'
                            ],
                            'file1' => 'file 1',
                            'file2' => 'file 2',
                            'file3' => 'file 3'
                        ]
                    ]
                ]
            ]
        ];

        vfsStream::setup('root', null, $structure);
        $pathBlueprint = vfsStream::url('root');

        $filesystem = new Filesystem();

        $filesystem->remove($this->vendorPath);
        $filesystem->mirror($pathBlueprint, $this->vendorPath);
    }

    private function buildDemoDataInstaller(): DemoDataInstaller
    {
        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath', 'getOutPath'])
            ->getMock();
        $facts->expects($this->any())->method('getVendorPath')->willReturn($this->vendorPath);
        $facts->expects($this->any())->method('getOutPath')->willReturn($this->targetPath);

        $edition = 'CE';
        $demoDataPathSelector = new DemoDataPathSelector($facts, $edition);

        $filesystem = new Filesystem();

        return new DemoDataInstaller($facts, $demoDataPathSelector, $filesystem);
    }

    private function countFiles($path): int
    {
        return count(array_diff(scandir($path), ['.', '..']));
    }
}
