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
require_once($CFG->dirroot . '/mod/qrhunt/phpqrcode/qrlib.php');

use PHPUnit\Framework\TestCase;

// command to run these tests - vendor/bin/phpunit tests/qr_tests.php
class qr_tests extends TestCase
{

    private $testImageNames = ['test_image.png', 'test_image_different.png'];

    protected function tearDown(): void
    {
        foreach ($this->testImageNames as $imageName) {
            $imagePath = __DIR__ . '/../qrcodes/' . $imageName;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

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

    public function testQrCodeGeneratedWrong()
    {
        // Set up test data
        $qrCodeData = "https://www.example.com";
        $imageName = "test_image";
        $differentQrCodeData = "https://www.example.org";

        // Generate the QR code images
        $imagePath = generate_qr_code_image($qrCodeData, $imageName);
        $differentImagePath = generate_qr_code_image($differentQrCodeData, $imageName . "_different");

        // Assert that the generated images exist
        $this->assertFileExists($imagePath);
        $this->assertFileExists($differentImagePath);

        // Assert that the generated images are not equal
        $this->assertNotEquals(
            file_get_contents($differentImagePath),
            file_get_contents($imagePath)
        );
    }

    public function testUserHasAnsweredCorrectly()
    {
        $DB = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_records', 'get_record'])
            ->getMock();

        $USER = new stdClass();
        $USER->id = 1;

        $moduleinstance = new stdClass();
        $moduleinstance->id = 2;

        $records = [
            (object) ['userid' => 1, 'correctanswer' => 1, 'answer' => 'answer1']
        ];

        $DB->expects($this->once())
            ->method('get_records')
            ->with('qrhunt_user_activity', ['userid' => $USER->id])
            ->willReturn($records);

        $DB->expects($this->once())
            ->method('get_record')
            ->with('qrhunt', ['id' => $moduleinstance->id])
            ->willReturn((object) ['answer' => 'answer1']);

        $this->assertTrue(has_user_answered_correctly($DB, $USER, $moduleinstance));
    }

    public function testUserHasNotAnsweredCorrectly()
    {
        $DB = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_records', 'get_record'])
            ->getMock();

        $USER = new stdClass();
        $USER->id = 1;

        $moduleinstance = new stdClass();
        $moduleinstance->id = 2;

        $records = [
            (object) ['userid' => 1, 'correctanswer' => 1, 'answer' => 'answer2']
        ];

        $DB->expects($this->once())
            ->method('get_records')
            ->with('qrhunt_user_activity', ['userid' => $USER->id])
            ->willReturn($records);

        $DB->expects($this->once())
            ->method('get_record')
            ->with('qrhunt', ['id' => $moduleinstance->id])
            ->willReturn((object) ['answer' => 'answer1']);

        $this->assertFalse(has_user_answered_correctly($DB, $USER, $moduleinstance));
    }

    public function testNoUserActivityRecords()
    {
        $DB = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_records', 'get_record'])
            ->getMock();

        $USER = new stdClass();
        $USER->id = 1;

        $moduleinstance = new stdClass();
        $moduleinstance->id = 2;

        $DB->expects($this->once())
            ->method('get_records')
            ->with('qrhunt_user_activity', ['userid' => $USER->id])
            ->willReturn([]);

        $DB->expects($this->once())
            ->method('get_record')
            ->with('qrhunt', ['id' => $moduleinstance->id])
            ->willReturn((object) ['answer' => 'answer1']);

        $this->assertFalse(has_user_answered_correctly($DB, $USER, $moduleinstance));
    }

    public function testWriteQrhuntUserGrade()
    {
        global $DB, $PAGE, $CFG;

        // Set up test data
        $moduleInstance = (object)array('name' => 'Test QR Hunt');
        $USER = (object)array('id' => 1);
        $PAGE = (object)array('course' => (object)array('id' => 1));
        $rawgrade = 80;

        // Call the function being tested
        write_qrhunt_user_grade($moduleInstance, $USER, $PAGE, $rawgrade, $CFG);

        // Assert that the grade has been added to the database
        $grade = $DB->get_record('grade_grades', array('userid' => $USER->id));
        $this->assertNotNull($grade);
        $this->assertEquals($rawgrade, $grade->rawgrade);

        // Assert that the grade item has been added to the database
        $grade_item = $DB->get_record('grade_items', array('itemname' => $moduleInstance->name));
        $this->assertNotNull($grade_item);
        $this->assertEquals(GRADE_TYPE_VALUE, $grade_item->gradetype);
        $this->assertEquals(100, $grade_item->grademax);
        $this->assertEquals(0, $grade_item->grademin);
    }
}
