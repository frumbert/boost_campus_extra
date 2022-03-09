<?php
// This file is part of Moodle - http://moodle.org/
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
 * Theme Boost Campus Child - Theme config
 *
 * @package    theme_boost_campus_extra
 * @copyright  2017 Kathrin Osswald, Ulm University <kathrin.osswald@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'boost_campus_extra';
$THEME->parents = ['boost_campus', 'boost'];
$THEME->editor_sheets = [];
$THEME->scss = function($theme) {
    return theme_boost_campus_extra_get_main_scss_content($theme);
};
$THEME->enable_dock = false;
$THEME->extrascsscallback = 'theme_boost_campus_extra_get_extra_scss';
$THEME->prescsscallback = 'theme_boost_campus_extra_get_pre_scss';
$THEME->yuicssmodules = array();
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = ' ';

// define two block areas - above content, and to the right (default)
$regions = array('main-top','side-pre');

// append/modify theme layouts we would like instead of boost_campus ones
$THEME->layouts['frontpage'] = array(
       'file' => 'columns2.php',
        'regions' => $regions,
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true, 'nocontextheader' => true),
    );
$THEME->layouts['scorm'] = array(
        'file' => 'columns2.php',
        'regions' => array(),
        'options' => array('nonavbar' => true, 'langmenu' => true, 'nocontextheader' => true, 'nofooter' => true, 'nocoursefooter' => true, 'minimal_header' => true),
    );
$THEME->layouts['login'] = array(
        'file' => 'login.php',
        'regions' => array(),
        'options' => array('langmenu' => true),
    );


// Get the config from parent theme boost_campus.
if (get_config('theme_boost_campus', 'addablockposition') == 'positionnavdrawer') {
    $THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
} else {
    $THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_DEFAULT;
}
// MODIFCATION END.
/* ORIGINAL START.
THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_DEFAULT;
ORIGINAL END. */
