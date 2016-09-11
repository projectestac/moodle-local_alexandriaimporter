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
 * Alexandria importer
 *
 * @package    local
 * @subpackage alexandriaimporter
 * @copyright  2016 Pau Ferrer Ocaña pau@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_once('locallib.php');

// The courseid we are importing to.
$courseid = required_param('id', PARAM_INT);
$dataid = optional_param('dataid', false, PARAM_INT);

// Load the course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

// Must pass login.
require_login($course);

// Must hold restoretargetimport in the current course.
require_capability('moodle/restore:restoretargetimport', $context);

// Set up the page.
$PAGE->set_title($course->shortname . ': ' . get_string('importfromalexandria', 'local_alexandriaimporter'));
$PAGE->set_heading($course->fullname);
$PAGE->set_url(new moodle_url('/local/alexandriaimporter/search.php', array('id' => $courseid)));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');

// Show the course selector.
echo $OUTPUT->header();

// Check if alexandria import is enabled.
$dbs = alexandria_get_databases();
if (count($dbs) <= 0) {
    echo $OUTPUT->notification(get_string('nodatabases', 'local_alexandriaimporter'), 'error');
    echo $OUTPUT->footer();
    die();
}

echo $OUTPUT->heading(get_string('importfromalexandria', 'local_alexandriaimporter'));
echo $OUTPUT->container_start();
$search = render_alexandria_searchform($dbs, $courseid, $dataid);
echo $OUTPUT->container_end();

if ($dataid) {
    $found = search_in_alexandria($dataid, $search);

    if (!empty($found)) {
        echo $OUTPUT->container_start();
        $data = $dbs[$dataid];

        echo $OUTPUT->heading(get_string('results', 'local_alexandriaimporter', $data->searching));
        if (!isset($found->results) || count($found->results) == 0) {
            echo $OUTPUT->notification(get_string('noresults', 'local_alexandriaimporter'), 'error');
            echo $OUTPUT->container_end();
            echo $OUTPUT->footer();
            die();
        }

        $url = new moodle_url('/local/alexandriaimporter/import.php',
            array(
                'id' => $courseid,
                'datatype' => $data->type,
                'fieldid' => $found->fieldid
            )
        );
        echo '<div class="accordion" id="accordion">';
        foreach ($found->results as $result) {
            $url->param('recordid', $result->id);
            $url->param('filename', $result->filename);
            echo format_record_contents($result->content, $result->id, $url);
        }
        echo '</div>';

        echo '<script src="'.$CFG->wwwroot.'/local/alexandriaimporter/search.js"></script>';

        echo $OUTPUT->container_end();
    }
}

echo $OUTPUT->footer();