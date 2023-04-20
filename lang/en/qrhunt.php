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
 * Plugin strings are defined here.
 *
 * @package     mod_qrhunt
 * @category    string
 * @copyright   2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'QR Hunt';
$string['modulename'] = 'QR Hunt';
$string['modulenameplural'] = 'QR HUNT';
$string['no$qrhuntinstances'] = ' No QR Hunt instances';
$string['qrhuntname'] = 'QR Hunt name';
$string['qrhuntsettings'] = 'QR Hunt settings';
$string['qrhuntname_help'] = 'Choose a name for you Qr Hunt';
$string['alwaysshowdescription'] = 'Always show description';
$string['grading'] = 'Grading';
$string['grade'] = 'Grade';
$string['gradetopass'] = 'Grade to pass';
$string['completionheader'] = 'Qr hunt completion';
$string['completionansweredcorrectly'] = 'Completion answered correctly';
$string['completionansweredcorrectlydesc'] = 'Track';
$string['completionansweredcorrectly_help'] = 'Mark this to track if user has answered corectly';

$string['qrhuntfieldset'] = 'Availability';
$string['allowattemptsfromdate'] = 'Allow attempts from';
$string['allowattemptsfromdate_help'] = 'If enabled, players will not be able to play before this date.';
$string['cutoffdate'] = 'Cut-off date';
$string['cutoffdate_help'] = 'If set, the QR hunt will not accept attempts after this date without an extension.';
$string['cutoffdatefromdatevalidation'] = 'Cut-off date must be after the allow submissions from date.';
$string['alwaysshowdescription_help'] = 'If disabled, the QR hunts Description above will only become visible to players
at the "Allow attempts from" date.';

$string['qrhuntclue'] = 'Clue';
$string['clue_updated'] = 'Clue has been updated';
$string['pluginadministration'] = 'Plugin administration';
$string['modulenameicon'] = '<img src="'.$CFG->wwwroot.'/mod/qrhunt/pix/icon.png" class="icon" alt="QR icon" />';

$string['qrhuntcluestarttext'] = 'Clue to the QR code: ';
$string['generatedqrcode'] = 'Generated QR code';
$string['downloadqrcode'] = 'Download QR code image';
$string['errorfilenotfound'] = 'Error finding QR code image';
$string['noqrimagefound'] = 'No QR image found';

$string['noanswergenerated'] = 'QR answer is not generated';
$string['correctanswermessage'] = 'Congratulations, your answer is correct press back to start to go back';
$string['incorrectanswermessage'] = 'Sorry, your answer is not correct';

$string['enterqranswer'] = 'Enter new QR answer';
$string['refreshqr'] = 'Refresh QR';
$string['successfulchange'] = 'QR code value has been changed to {$a}';

$string['entercluetext'] = 'Enter new clue text';
$string['updatecluetext'] = 'Update clue text';

$string['gametitle'] = 'Submit your answer';
$string['enteranswer'] = 'Enter your answer or scan a QR code';
$string['submitanswer'] = 'Submit answer';
$string['startcamera'] = 'Start camera';
$string['stopcamera'] = 'Stop camera';

$string['play'] = 'Play';
$string['back'] = 'Back to start';

$string['completion'] = 'Set completion';
$string['completionnone'] = 'None';
$string['completionmanual'] = 'Manual completion';
$string['completionautomatic'] = 'Automatic completion';
$string['custom_completion_condition'] = 'Custom completion condition';