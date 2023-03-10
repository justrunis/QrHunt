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
$string['alwaysshowdescription'] = 'Alyaws show description';

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

