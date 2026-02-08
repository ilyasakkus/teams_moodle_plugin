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
 * Activity creation/editing form.
 *
 * "Create Meeting" button opens a popup with the Enovation meeting app.
 * The meeting URL is captured automatically via postMessage relay.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 */
class mod_teamsmeeting_mod_form extends moodleform_mod {

    /**
     * Defines forms elements.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Build popup URL.
        $popupurl = new moodle_url('/mod/teamsmeeting/popup.php', ['sesskey' => sesskey()]);

        // Also get the direct meeting app URL for the fallback link.
        $meetingappurl = get_config('mod_teamsmeeting', 'meetingappurl');
        if (empty($meetingappurl)) {
            $meetingappurl = 'https://enomsteams.z16.web.core.windows.net';
        }

        // ---- General section ----
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('meetingname', 'mod_teamsmeeting'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // ---- Meeting Link section ----
        $mform->addElement('header', 'meetinglinksection', get_string('meetinglink', 'mod_teamsmeeting'));
        $mform->setExpanded('meetinglinksection', true);

        // Hidden field for the URL.
        $mform->addElement('hidden', 'externalurl', '');
        $mform->setType('externalurl', PARAM_URL);

        // Meeting creation UI.
        $html = '
        <div id="tm-creator">

            <!-- No URL state -->
            <div id="tm-empty">
                <button type="button" class="btn btn-primary btn-lg" id="tm-create-btn"
                        onclick="tmOpenPopup()" style="background:#6264A7;border-color:#6264A7;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"
                         style="margin-right:8px;vertical-align:text-bottom;">
                        <path d="M19.27 7.04L15.5 9.5V7.5C15.5 6.95 15.05 6.5 14.5 6.5H7.5C6.95 6.5 6.5 6.95 6.5 7.5V14.5C6.5 15.05 6.95 15.5 7.5 15.5H14.5C15.05 15.5 15.5 15.05 15.5 14.5V12.5L19.27 14.96C19.72 15.24 20.29 14.92 20.29 14.39V7.61C20.29 7.08 19.72 6.76 19.27 7.04Z"/>
                    </svg>
                    ' . get_string('createmeetingbtn', 'mod_teamsmeeting') . '
                </button>
                <p class="text-muted mt-2 mb-1" style="font-size:13px;">
                    ' . get_string('createmeetinghelp', 'mod_teamsmeeting') . '
                </p>
                <p class="mb-0" style="font-size:12px;">
                    <a href="#" onclick="tmToggleManual();return false;" style="color:#6264A7;">
                        ' . get_string('enterurlmanually', 'mod_teamsmeeting') . '
                    </a>
                </p>

                <!-- Manual entry (hidden by default) -->
                <div id="tm-manual" style="display:none;margin-top:10px;">
                    <div class="input-group" style="max-width:600px;">
                        <input type="text" class="form-control" id="tm-manual-input"
                               placeholder="https://teams.microsoft.com/l/meetup-join/...">
                        <button type="button" class="btn btn-success" onclick="tmUseManual()">
                            ' . get_string('useurl', 'mod_teamsmeeting') . '
                        </button>
                    </div>
                    <div id="tm-manual-error" class="text-danger" style="display:none;font-size:12px;margin-top:4px;">
                        ' . get_string('invalidteamsurl', 'mod_teamsmeeting') . '
                    </div>
                </div>
            </div>

            <!-- URL captured state -->
            <div id="tm-captured" style="display:none;">
                <div class="alert alert-success d-flex align-items-center mb-2" style="max-width:600px;">
                    <svg viewBox="0 0 16 16" width="20" height="20" fill="currentColor"
                         class="flex-shrink-0" style="margin-right:10px;">
                        <path d="M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z"/>
                    </svg>
                    <strong>' . get_string('urlset', 'mod_teamsmeeting') . '</strong>
                </div>
                <div class="input-group" style="max-width:600px;">
                    <input type="text" class="form-control bg-light" id="tm-url-show" readonly>
                    <button type="button" class="btn btn-outline-secondary" onclick="tmClear()">
                        ' . get_string('changeurl', 'mod_teamsmeeting') . '
                    </button>
                </div>
            </div>

        </div>';

        $mform->addElement('html', $html);

        // ---- Schedule (optional) ----
        $mform->addElement('header', 'meetingschedule', get_string('meetingschedule', 'mod_teamsmeeting'));

        $mform->addElement('date_time_selector', 'meetingstart',
            get_string('meetingstart', 'mod_teamsmeeting'), ['optional' => true]);

        $mform->addElement('date_time_selector', 'meetingend',
            get_string('meetingend', 'mod_teamsmeeting'), ['optional' => true]);

        // ---- Standard elements ----
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();

        // ---- JavaScript ----
        $popupurljs = $popupurl->out(false);

        $mform->addElement('html', '
        <script>
        (function() {
            var POPUP_URL = ' . json_encode($popupurljs) . ';
            var popup = null;

            // Open popup with meeting app.
            window.tmOpenPopup = function() {
                var w = Math.min(900, screen.width - 100);
                var h = Math.min(700, screen.height - 100);
                var left = (screen.width - w) / 2;
                var top = (screen.height - h) / 2;
                popup = window.open(
                    POPUP_URL,
                    "teamsmeeting",
                    "width=" + w + ",height=" + h + ",left=" + left + ",top=" + top +
                    ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes"
                );
                if (!popup || popup.closed) {
                    popup = window.open(POPUP_URL, "_blank");
                }
            };

            // Listen for postMessage from popup.
            window.addEventListener("message", function(e) {
                if (e.data && e.data.type === "teamsmeeting_url" && e.data.url) {
                    setUrl(e.data.url);
                }
            });

            // Toggle manual entry.
            window.tmToggleManual = function() {
                var el = document.getElementById("tm-manual");
                el.style.display = el.style.display === "none" ? "block" : "none";
            };

            // Use manually entered URL.
            window.tmUseManual = function() {
                var url = (document.getElementById("tm-manual-input").value || "").trim();
                if (isTeamsUrl(url)) {
                    document.getElementById("tm-manual-error").style.display = "none";
                    setUrl(url);
                } else {
                    document.getElementById("tm-manual-error").style.display = "block";
                }
            };

            // Clear URL.
            window.tmClear = function() {
                setHidden("");
                document.getElementById("tm-empty").style.display = "block";
                document.getElementById("tm-captured").style.display = "none";
                document.getElementById("tm-manual-input").value = "";
            };

            function setUrl(url) {
                setHidden(url);
                document.getElementById("tm-url-show").value = url;
                document.getElementById("tm-empty").style.display = "none";
                document.getElementById("tm-captured").style.display = "block";
            }

            function setHidden(val) {
                var el = document.querySelector("input[name=\'externalurl\']");
                if (el) el.value = val;
            }

            function isTeamsUrl(s) {
                return s && (s.indexOf("teams.microsoft.com") !== -1 || s.indexOf("teams.live.com") !== -1);
            }

            // Init: show captured state if URL already set.
            document.addEventListener("DOMContentLoaded", function() {
                var el = document.querySelector("input[name=\'externalurl\']");
                if (el && el.value && el.value.trim()) setUrl(el.value.trim());
            });

            // Enter key on manual input.
            var mi = document.getElementById("tm-manual-input");
            if (mi) mi.addEventListener("keydown", function(e) {
                if (e.key === "Enter") { e.preventDefault(); tmUseManual(); }
            });
        })();
        </script>');
    }

    /**
     * Validates form data.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['meetingstart']) && !empty($data['meetingend'])) {
            if ($data['meetingend'] <= $data['meetingstart']) {
                $errors['meetingend'] = get_string('errorendbeforestart', 'mod_teamsmeeting');
            }
        }
        return $errors;
    }

    /**
     * Pre-process form data before display.
     */
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);
    }
}
