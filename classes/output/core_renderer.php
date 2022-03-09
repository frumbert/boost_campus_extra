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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package   theme_boost_campus_extra
 * @copyright 2017 Kathrin Osswald, Ulm University kathrin.osswald@uni-ulm.de
 *            copyright based on code from theme_boost by Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_campus_extra\output;

use moodle_url;
use theme_config;

defined('MOODLE_INTERNAL') || die;


/**
 * Extending the core_renderer interface.
 *
 * @copyright 2020 Kathrin Osswald, Ulm University kathrin.osswald@uni-ulm.de
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package theme_boost_campus_extra
 * @category output
 */
class core_renderer extends \theme_boost_campus\output\core_renderer {

   /**
     * Override to add additional class for the random login image to the body.
     *
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * KIZ MODIFICATION: This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @since Moodle 2.5.1 2.6
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     * @return string
     */
    public function body_attributes($additionalclasses = array()) {
        global $CFG;
        require_once($CFG->dirroot . '/theme/boost_campus/locallib.php');

        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }

        // MODIFICATION START.
        // Only add classes for the login page.
        if ($this->page->bodyid == 'page-login-index' || $this->page->bodyid == 'page-login-signup') {
            $additionalclasses[] = 'loginbackgroundimage';
            // Generating a random class for displaying a random image for the login page.
            $additionalclasses[] = theme_boost_campus_get_random_loginbackgroundimage_class();
        }
        // MODIFICATION END.

        return ' id="'. $this->body_id().'" class="'.$this->body_css_classes($additionalclasses).'"';
    }
    
    /**
     * Wrapper for header elements.
     *
     * Prevents the header from rendering the logo again
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        // MODIFICATION START.
        global $USER, $COURSE, $PAGE;
        // MODIFICATION END.

        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                    $this->region_main_settings_menu(),
                    'd-print-none',
                    ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new \stdClass();
        // MODIFICATION START.
        // Show the context header settings menu on all pages except for the profile page as we replace
        // it with an edit button there and if we are not on the content bank view page (contentbank/view.php)
        // as this page only adds header actions.
        if ($this->page->pagelayout != 'mypublic' && $this->page->bodyid != 'page-contentbank') {
            $header->settingsmenu = $this->context_header_settings_menu();
        }
        // MODIFICATION END.
        // @codingStandardsIgnoreStart
        /* ORIGINAL START
        $header->settingsmenu = $this->context_header_settings_menu();
        ORIGINAL END. */
        // @codingStandardsIgnoreEnd
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        // MODIFICATION START.
        // Show the page heading button on all pages except for the profile page.
        // There we replace it with an edit profile button.
        if ($this->page->pagelayout != 'mypublic') {
            $header->pageheadingbutton = $this->page_heading_button();
        } else {
            // Get the id of the user for whom the profile page is shown.
            $userid = optional_param('id', $USER->id, PARAM_INT);
            // Check if the shown and the operating user are identical.
            $currentuser = $USER->id == $userid;
            if (($currentuser || is_siteadmin($USER) || !is_siteadmin($userid)) &&
                has_capability('moodle/user:update', \context_system::instance())) {
                $url = new moodle_url('/user/editadvanced.php', array('id'       => $userid, 'course' => $COURSE->id,
                                                                      'returnto' => 'profile'));
                $header->pageheadingbutton = $this->single_button($url, get_string('editmyprofile', 'core'));
            } else if ((has_capability('moodle/user:editprofile', \context_user::instance($userid)) &&
                    !is_siteadmin($USER)) || ($currentuser &&
                    has_capability('moodle/user:editownprofile', \context_system::instance()))) {
                $url = new moodle_url('/user/edit.php', array('id'       => $userid, 'course' => $COURSE->id,
                                                              'returnto' => 'profile'));
                $header->pageheadingbutton = $this->single_button($url, get_string('editmyprofile', 'core'));
            }
        }
        // MODIFICATION END.
        // @codingStandardsIgnoreStart
        /* ORIGINAL START
        $header->pageheadingbutton = $this->page_heading_button();
        ORIGINAL END. */
        // @codingStandardsIgnoreEnd

        // tim:
        if ($PAGE->pagelayout === "scorm") {
	        $header->courseheader = $COURSE->fullname;
        } else {
	        $header->courseheader = $this->course_header();
        }

        // Don't show the logo in the front page header (it's already on the navbar)
        if (strpos($header->contextheader, '/pluginfile.php/1/core_admin/logo/') !== false) {
            $header->contextheader = null;

        }
        // < ITTA

        $header->headeractions = $this->page->get_header_actions();
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * Override to be able to use uploaded images from admin_setting as well.
     *
     * Returns the moodle_url for the favicon.
     *
     * KIZ MODIFICATION: This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     */
    public function favicon() {
        // MODIFICATION START.
        // Get the theme Boost Campus config.
        $bcconfig = theme_config::load('boost_campus');
        // Get the theme Boost Campus favicon setting.
        $bcconffavicon = get_config('theme_boost_campus', 'favicon');
        if (!empty($bcconffavicon)) {
            // Return the image that was saved in the theme Boost Campus favicon setting.
            return $bcconfig->setting_file_url('favicon', 'favicon');
        } else {
            // Return the icon stored in boost_campus/pix folder.
            return $bcconfig->image_url('favicon', 'theme');
        }
        // MODIFICATION END.
        // @codingStandardsIgnoreStart
        /* ORIGINAL START.
        return $this->image_url('favicon', 'theme');
        ORIGINAL END. */
        // @codingStandardsIgnoreEnd
    }

    // Always show the main logo (navbar)
    public function should_display_navbar_logo() {
        $logo = $this->get_logo_url();
        return !empty($logo);
    }

    // Always use the large logo (navbar)
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100) {
        return parent::get_logo_url();
    }

    // ITTA - cause clicking a onetopic course in the navbar to open section 0 of that course
    public function navbar() {
        global $COURSE, $PAGE;
        if ($COURSE->id !== SITEID && $COURSE->format === "onetopic") {
            foreach ($PAGE->navbar->get_items() as &$node) {
                if ($node->type === \navigation_node::TYPE_COURSE && $node->action instanceof moodle_url) {
                    $action = $node->action->param('section','0');
                }
            }
        }
        return $this->render_from_template('core/navbar', $this->page->navbar);
    }
}
