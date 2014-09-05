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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_skype
 * @copyright 2011 Amr Hourani a.hourani@gmail.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// Replace skype with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$PAGE->set_url('/mod/skype/index.php', array('id'=>$id));

if (! $course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

require_course_login($course);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'skype', 'view all', "index.php?id=$course->id", '');

$params = array(
    'context' => context_course::instance($id)
);
//$event = \mod_skype\event\instances_list_viewed::create($params);
//$event->trigger();

/// Get all required strings

$strskypes = get_string('modulenameplural', 'skype');
$strskype  = get_string('modulename', 'skype');


/// Print the header
$PAGE->navbar->add($strskypes);
$PAGE->set_title($strskypes);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($strskypes, 2);

/// Get all the appropriate data

if (! $skypes = get_all_instances_in_course('skype', $course)) {
    notice(get_string('thereareno', 'moodle', $strskypes), "../../course/view.php?id=$course->id");
    die();
}

$usesections = course_format_uses_sections($course->format);

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

$table = new html_table();

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname);
    $table->align = array ('center', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left');
}

$currentsection = '';
foreach ($skypes as $skype) {
    if (!$skype->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$skype->coursemodule\">".format_string($skype->name,true)."</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$skype->coursemodule\">".format_string($skype->name,true)."</a>";
    }
    $printsection = '';
    if ($skype->section !== $currentsection) {
        if ($skype->section) {
            $printsection = get_section_name($course, $skype->section);
        }
        if ($currentsection !== '') {
            $table->data[] = 'hr';
        }
        $currentsection = $skype->section;
    }
    if ($usesections) {
        $table->data[] = array ($printsection, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo '<br />';

echo html_writer::table($table);

/// Finish the page

echo $OUTPUT->footer();
