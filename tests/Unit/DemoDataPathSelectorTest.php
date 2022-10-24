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

use OxidEsales\DemoDataInstaller\DemoDataPathSelector;
use PHPUnit\Framework\TestCase;

final class DemoDataPathSelectorTest extends TestCase
{
    /**
     * @dataProvider providerGetPathWithoutVendorPath
     */
    public function testGetPathWithoutVendorPath($edition, $expectedPath): void
    {
        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath'])
            ->getMock();
        $demoDataPathSelector = new DemoDataPathSelector($facts, $edition);
        $this->assertEquals($expectedPath, $demoDataPathSelector->getPath());
    }

    public function providerGetPathWithoutVendorPath(): array
    {
        return [
            ['CE', $this->getPathToOut('o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-ce')],
            ['PE', $this->getPathToOut('o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-pe')],
            ['EE', $this->getPathToOut('o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-ee')]
        ];
    }

    /**
     * @dataProvider providerGetPathWithVendorPath
     */
    public function testGetPathWithVendorPath($edition, $expectedPath): void
    {
        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath'])
            ->getMock();
        $facts->expects($this->any())->method('getVendorPath')->willReturn('vendor');
        $demoDataPathSelector = new DemoDataPathSelector($facts, $edition);
        $this->assertEquals($expectedPath, $demoDataPathSelector->getPath());
    }

    public function providerGetPathWithVendorPath(): array
    {
        return [
            ['CE', $this->getPathToOut('vendor' . DIRECTORY_SEPARATOR . 'o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-ce')],
            ['PE', $this->getPathToOut('vendor' . DIRECTORY_SEPARATOR . 'o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-pe')],
            ['EE', $this->getPathToOut('vendor' . DIRECTORY_SEPARATOR . 'o3-shop' . DIRECTORY_SEPARATOR . 'shop-demodata-ee')]
        ];
    }

    /**
     * Concat base path to the path to out directory.
     *
     * @param string $basePath
     *
     * @return string
     */
    private function getPathToOut($basePath): string
    {
        $fullPath = [$basePath, 'src', 'out'];

        return implode(DIRECTORY_SEPARATOR, $fullPath);
    }
}
