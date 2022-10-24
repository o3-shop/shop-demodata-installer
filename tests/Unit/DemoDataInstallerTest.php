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

namespace OxidEsales\DemoDataInstaller\Tests\Unit;

use org\bovigo\vfs\vfsStream;
use OxidEsales\DemoDataInstaller\DemoDataInstaller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class DemoDataInstallerTest extends TestCase
{
    public function testNoDemoDataFound(): void
    {
        $structure = [
            'source' => [
                'out' => []
            ],
            'vendor' => [
                'demodata-directory' => []
            ]
        ];

        vfsStream::setup('root', null, $structure);
        $outPath = $this->getOutPath();
        $demoDataInstaller = $this->buildDemoDataInstaller();

        $this->assertSame(0, $demoDataInstaller->execute());
        $this->assertSame(0, $this->countFiles($outPath), 'Directory is not empty.');
    }

    public function testDemoDataExist(): void
    {
        $structure = [
            'source' => [
                'out' => [
                    'file1',
                    'file2',
                    'file3'
                ]
            ],
            'vendor' => [
                'demodata-directory' => []
            ]
        ];

        vfsStream::setup('root', null, $structure);
        $outPath = $this->getOutPath();
        $demoDataInstaller = $this->buildDemoDataInstaller();

        $this->assertSame(0, $demoDataInstaller->execute());
        $this->assertSame(3, $this->countFiles($outPath), 'Files have not been copied.');
    }

    public function testErrorOccurs(): void
    {
        $filesystem = $this->getMockBuilder('Filesystem')
            ->setMethods(['mirror'])
            ->getMock();
        $filesystem->expects($this->any())->method('mirror')->willThrowException(new IOException('Test'));
        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath', 'getOutPath'])
            ->getMock();
        $demoDataPathSelector = $this->getMockBuilder('DemodataPathSelector')
            ->setMethods(['getPath'])
            ->getMock();

        $demoDataInstaller = new DemoDataInstaller($facts, $demoDataPathSelector, $filesystem);

        $this->assertSame(1, $demoDataInstaller->execute());
    }

    private function getOutPath(): string
    {
        return vfsStream::url('root/source/out');
    }

    private function buildDemoDataInstaller(): DemoDataInstaller
    {
        $outPath = $this->getOutPath();
        $demoDataPath = vfsStream::url('root/vendor/demodata-directory');

        $filesystem = new Filesystem();

        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath', 'getOutPath'])
            ->getMock();
        $facts->expects($this->any())->method('getOutPath')->willReturn($outPath);

        $demoDataPathSelector = $this->getMockBuilder('DemodataPathSelector')
            ->setMethods(['getPath'])
            ->getMock();
        $demoDataPathSelector->expects($this->any())->method('getPath')->willReturn($demoDataPath);

        return new DemoDataInstaller($facts, $demoDataPathSelector, $filesystem);
    }

    private function countFiles($path): int
    {
        return count(array_diff(scandir($path), ['.', '..']));
    }
}
