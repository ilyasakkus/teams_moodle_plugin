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
 * Plugin administration settings.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    if ($ADMIN->fulltree) {

        // Meeting App Settings header.
        $settings->add(new admin_setting_heading(
            'mod_teamsmeeting/appheading',
            get_string('meetingappsettings', 'mod_teamsmeeting'),
            get_string('meetingappsettings_desc', 'mod_teamsmeeting')
        ));

        // Meeting App URL.
        $settings->add(new admin_setting_configtext(
            'mod_teamsmeeting/meetingappurl',
            get_string('meetingappurl', 'mod_teamsmeeting'),
            get_string('meetingappurl_desc', 'mod_teamsmeeting'),
            'https://enomsteams.z16.web.core.windows.net',
            PARAM_URL
        ));

        // How the meeting opens for students.
        $settings->add(new admin_setting_heading(
            'mod_teamsmeeting/displayheading',
            get_string('displaysettings', 'mod_teamsmeeting'),
            get_string('displaysettings_desc', 'mod_teamsmeeting')
        ));

        // Always open in new tab (recommended).
        $settings->add(new admin_setting_configcheckbox(
            'mod_teamsmeeting/openinnewtab',
            get_string('openinnewtab', 'mod_teamsmeeting'),
            get_string('openinnewtab_desc', 'mod_teamsmeeting'),
            1
        ));

        // PNA/CORS workaround info.
        $settings->add(new admin_setting_heading(
            'mod_teamsmeeting/pnaheading',
            get_string('pnainfo', 'mod_teamsmeeting'),
            get_string('pnainfo_desc', 'mod_teamsmeeting')
        ));
    }
}
