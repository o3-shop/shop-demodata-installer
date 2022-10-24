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

use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class responsible to copy demo images from needed O3-Shop edition in vendor
 * to the ``OUT`` directory so they would be browser accessible.
 */
class DemoDataInstaller
{
    /** @var string path to demo data directory */
    private $demoDataPath = null;

    /** @var string path to directory where to copy files to */
    private $outPath = null;

    /** @var \Symfony\Component\Filesystem\Filesystem filesystem component */
    private $filesystem = null;

    /**
     * Initialize with all needed dependencies.
     *
     * @param \OxidEsales\Facts\Facts $facts to get path to O3-Shop OUT directory.
     * @param \OxidEsales\DemoDataInstaller\DemoDataPathSelector $demoDataPathSelector to get path to demo data directory.
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem dependency which does actual copying.
     */
    public function __construct($facts, $demoDataPathSelector, $filesystem)
    {
        $this->demoDataPath = $demoDataPathSelector->getPath();
        $this->outPath = $facts->getOutPath();
        $this->filesystem = $filesystem;
    }

    /**
     * Copies DemoData images from vendor directory of needed edition
     * to the O3-Shop ``OUT`` directory.
     *
     * @return int error code
     */
    public function execute()
    {
        try {
            $this->filesystem->mirror($this->demoDataPath, $this->outPath);
        } catch (IOException $exception) {
            $items = [
                "Error occurred while copying files:",
                $exception->getMessage(),
                "\n"
            ];
            $message = implode(" ", $items);
            echo $message;
            return 1;
        }
        return 0;
    }
}
