<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_qrhunt
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */

define('CLI_SCRIPT', true);
require(__DIR__.'/../../../config.php');
global $CFG;
require_once("$CFG->dirroot/mod/qrhunt/lib.php");

use PHPUnit\Framework\TestCase;

// command to run these tests vendor/bin/phpunit tests/location_tests.php
class qr_tests extends TestCase
{
    public function testQrCodeGeneratedSuccessfully() {
        $qrCodeData = 'https://example.com';
        $imageName = 'test_qr_code';

        // Call the function being tested.
        $imagePath = generate_qr_code_image($qrCodeData, $imageName);

        // Assert that the image was generated and saved successfully.
        $this->assertFileExists($imagePath);
        $this->assertStringEndsWith('.png', $imagePath);

        // Assert that the generated image is a valid PNG image.
        $this->assertNotFalse(@imagecreatefrompng($imagePath));
    }
}
