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
 * Define all the backup steps that will be used by the backup_skype_activity_task.
 *
 * @package mod_skype
 * @copyright 2016 onwards AL Rachels (drachels@drachels.co9m)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete skype structure for backup, with file and id annotations
 *
 * @package mod_skype
 * @copyright 2016 onwards AL Rachels (drachels@drachels.co9m)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_skype_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure for the assign activity
     * @return void
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $skype = new backup_nested_element('skype', array('id'), array(
                                              'name',
                                              'intro',
                                              'introformat',
                                              'timecreated',
                                              'timemodified',
                                              'timeopen',
                                              'timeclose',
                                              'usepassword',
                                              'password',
                                              'exercise',
                                              'lesson',
                                              'isexam',
                                              'requiredgoal',
                                              'requiredwpm',
                                              'timelimit',
                                              'layout',
                                              'showkeyboard',
                                              'continuoustype',
                                              'countmistypedspaces',
                                              'statsbgc',
                                              'keytoptextc',
                                              'keytopbgc',
                                              'keybdbgc',
                                              'textalign',
                                              'coursorcolor',
                                              'textbgc',
                                              'texterrorcolor'.
                                              'countmistakes'));

        $attempts = new backup_nested_element('attempts');

        $attempt = new backup_nested_element('attempt', array('id'), array(
                                             'skypeid',
                                             'userid',
                                             'timetaken',
                                             'inprogress',
                                             'suspicion'));

        $checks = new backup_nested_element('checks');

        $check = new backup_nested_element('check', array('id'), array(
                                           'attemptid',
                                           'mistakes',
                                           'hits',
                                           'checktime'));

        $exercises = new backup_nested_element('exercises');

        $exercise = new backup_nested_element('exercise', array('id'), array(
                                             'texttotype',
                                             'exercisename',
                                             'lesson',
                                             'snumber'));

        $grades = new backup_nested_element('grades');

        $grade = new backup_nested_element('grade', array('id'), array(
                                                 'skype',
                                                 'userid',
                                                 'grade',
                                                 'mistakes',
                                                 'timeinseconds',
                                                 'hitsperminute',
                                                 'fullhits',
                                                 'precisionfield',
                                                 'timetaken',
                                                 'exercise',
                                                 'pass',
                                                 'attemptid',
                                                 'wpm',
                                                 'mistakedetails'));

        $layouts = new backup_nested_element('layouts');

        $layout = new backup_nested_element('layout', array('id'), array(
                                            'filepath',
                                            'name',
                                            'jspath'));

        $lessons = new backup_nested_element('lessons');

        $lesson = new backup_nested_element('lesson', array('id'), array(
                                            'lessonname',
                                            'authorid'.
                                            'visible',
                                            'editable',
                                            'courseid'));
        // Build the tree.
        $skype->add_child($attempts);
        $attempts->add_child($attempt);

        $skype->add_child($checks);
        $checks->add_child($check);

        $skype->add_child($grades);
        $grades->add_child($grade);

        $skype->add_child($layouts);
        $layouts->add_child($layout);

        $skype->add_child($lessons);
        $lessons->add_child($lesson);

        // Define sources.
        $skype->set_source_table('skype', array('id' => backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $grade->set_source_table('skype_grades', array('skype' => backup::VAR_PARENTID));
        }

        // Define id annotations.
        $grade->annotate_ids('skype', 'skype');
        $grade->annotate_ids('user', 'userid');

        // Define file annotations.
        // These file areas don't have an itemid.
        $skype->annotate_files('mod_skype', 'intro', null);

        // Return the root element (skype), wrapped into standard activity structure.
        return $this->prepare_activity_structure($skype);
    }
}
