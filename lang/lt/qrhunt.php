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

$string['pluginname'] = 'QR medžioklė';
$string['modulename'] = 'QR medžioklė';
$string['modulenameplural'] = 'QR MEDŽIOKLĖ';
$string['no$qrhuntinstances'] = ' Nėra QR medžioklės atvejų';
$string['qrhuntname'] = 'QR medžioklės pavadinimas';
$string['qrhuntsettings'] = 'QR medžioklės nustatymai';
$string['qrhuntname_help'] = 'Pasirinkite vardą savo Qr medžioklei';
$string['alwaysshowdescription'] = 'Rodyti aprašymą';
$string['grading'] = 'Įverčiai';
$string['grade'] = 'Pažimys';
$string['gradetopass'] = 'Išlaikymo įvertis';
$string['completionheader'] = 'Qr mežioklės užbaigimas';
$string['completionansweredcorrectly'] = 'Užbaigimas atsakytas teisingai';
$string['completionansweredcorrectlydesc'] = 'Sekti';
$string['completionansweredcorrectly_help'] = 'Pažymėti kad sekti ar žaidėjas atsakė teisingai';

$string['qrhuntfieldset'] = 'Prieinamumas';
$string['allowattemptsfromdate'] = 'Leisti nuo';
$string['allowattemptsfromdate_help'] = 'Jei įjungta, žaidėjai negalės žaisti iki šios datos.';
$string['cutoffdate'] = 'Pabaigos data';
$string['cutoffdate_help'] = 'Jei nustatyta, QR medžioklė nepriims bandymų po šios datos be pratęsimo.';
$string['cutoffdatefromdatevalidation'] = 'Galutinė data turi būti vėlesnė nei pateikimo leidimo data.';
$string['alwaysshowdescription_help'] = 'Jei išjungta, aukščiau esantis QR medžioklės aprašymas bus matomas tik žaidėjams 
nuo paskirtos datos.';

$string['qrhuntclue'] = 'Užuomina';
$string['clue_updated'] = 'Užuomina buvo atnaujinta';
$string['pluginadministration'] = 'Įskiepio administratorius';
$string['modulenameicon'] = '<img src="'.$CFG->wwwroot.'/mod/qrhunt/pix/icon.png" class="icon" alt="QR icon" />';

$string['qrhuntcluestarttext'] = 'Užuomina apie kodą: ';
$string['generatedqrcode'] = 'Sugeneruotas QR kodas';
$string['downloadqrcode'] = 'Atsisiųsti QR kodo nuotrauką';
$string['errorfilenotfound'] = 'Nerastas QR kodo failas';
$string['noqrimagefound'] = 'Nerasta QR kodo nuotrauka';

$string['noanswergenerated'] = 'Nėra sugeneruotas atsakymo QR kodas';
$string['correctanswermessage'] = 'Sveikiname, jūsų atsakymas teisingas, paspauskite atgal į pradžią, kad grįžti atgal';
$string['incorrectanswermessage'] = 'Atsiprašome, jūsų atsakymas neteisingas';
$string['noanswererror'] = 'Norėdami pateikti atsakymą, turite nuskaityti QR kodą';

$string['enterqranswer'] = 'Įveskite naują QR atsakymą';
$string['refreshqr'] = 'Atnaujinti QR';
$string['successfulchange'] = 'QR kodo reikšmė pakeista į {$a}';

$string['entercluetext'] = 'Įveskite naują užuominos tekstą';
$string['updatecluetext'] = 'Atnaujinti užuominos tekstą';

$string['gametitle'] = 'Pateikti savo atsakymą';
$string['enteranswer'] = 'Nuskaitykite QR kodą tam kad pateikti atsakymą';
$string['submitanswer'] = 'Pateikti atsakymą';
$string['startcamera'] = 'Įjungti kamerą';
$string['stopcamera'] = 'Išjungti kamerą';
$string['switchcamera'] = 'Pakeisti kamerą';

$string['play'] = 'Žaisti';
$string['back'] = 'Atgal į pradžią';

$string['invalidinputparameters'] = 'Netinkami įvesties parametrai';
$string['invalidgradevalue'] = 'Netinkama pažymio reikšmė';

$string['completion'] = 'Nustatyti užbaigimą';
$string['completionnone'] = 'Nėra';
$string['completionmanual'] = 'Rankinis užbaigimas';
$string['completionautomatic'] = 'Automatinis užbaigimas';
$string['custom_completion_condition'] = 'Pasirinktinos užbaigimo sąlygos';