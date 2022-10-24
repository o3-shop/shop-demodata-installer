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

namespace OxidEsales\DemoDataInstaller;

/**
 * Class responsible fo form path to the directory of O3-Shop demo data by active edition.
 */
class DemoDataPathSelector
{
    /** @var \OxidEsales\Facts\Facts object providing environment information. */
    private $facts;

    /** @var string O3-Shop edition which demo data to use. */
    private $edition = null;

    /**
     * DemoDataPathSelector constructor.
     *
     * @param \OxidEsales\Facts\Facts $facts
     * @param string $edition
     */
    public function __construct($facts, $edition)
    {
        $this->facts = $facts;
        $this->edition = $edition;
    }

    /**
     * @return string path to demo data directory.
     */
    public function getPath()
    {
        $path = [];
        $vendorPath = $this->facts->getVendorPath();
        if ($vendorPath) {
            $path[] = $vendorPath;
        }
        array_push($path,
            'o3-shop',
            'shop-demodata-' . strtolower($this->edition),
            'src',
            'out'
        );
        return implode(DIRECTORY_SEPARATOR, $path);
    }
}
