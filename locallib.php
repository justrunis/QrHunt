<?php

use core\notification;
use core\session\exception;
use mod_treasurehunt\model\stage;

// This file is part of Treasurehunt for Moodle
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions used by the qrhunt module.
 *
 * This contains functions that are called from within the qrhunt module only
 * Functions that are also called by core Moodle are in {@link lib.php}
 *
 * @package   mod_qrhunt
 * @copyright 2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @author 2023 Justinas Runevicius <justinas.runevicius@distance.ktu.lt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function qrhunt_view_info($qrhunt, $courseid)
{
    global $PAGE, $DB;
    $timenow = time();

    $roads = $DB->get_records('qrhunt', ['id' => $qrhunt->id]);
    $output = $PAGE->get_renderer('mod_qrhunt');
    list($select, $params) = $DB->get_in_or_equal(array_keys($roads));
    $select = "roadid $select and qrtext <> ''";
    $hasqr = $DB->count_records_select('qrhunt', $select, $params, 'count(qrtext)');
    $renderable = new treasurehunt_info($qrhunt, $timenow, $courseid, $roads, $hasqr);
    return $output->render($renderable);
}