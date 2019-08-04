<?php

//
// Display the whole course as "sections" made of of modules
// This is based on "topics" format, defaults to showing 1 section at a time.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir . '/ajax/ajaxlib.php');
require_once($CFG->libdir . '/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('topic', 0, PARAM_INT)) {
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

// make sure all sections are created
$course = course_get_format($course)->get_course();
course_create_sections_if_missing($course, range(0, $course->numsections));

// Enforce single section per page display.
$course->coursedisplay = COURSE_DISPLAY_SINGLEPAGE;

// Remember page for the duration of the session.
$home = optional_param('home', 0, PARAM_INT);
if (!empty($home)) {
    $section = 0;
    $_SESSION['current_topic_'.$course->id] = 0;
} else {
    $rawsection = optional_param('section', -1, PARAM_CLEAN);
    if($rawsection == 'all') {
        $section = -2;
    } else {
        $section = intval($rawsection);
    }
    if ($section == -1 && isset($_SESSION['current_topic_'.$course->id])) {
        $section = $_SESSION['current_topic_'.$course->id];
    } else {
        $_SESSION['current_topic_'.$course->id] = $section;
    }
}

if($section != -1) {
    $displaysection = $section;
}

$renderer = $PAGE->get_renderer('format_sections');

if ($displaysection == -2) {
    $renderer->print_multiple_section_page($course, null, null, null, null);
} elseif (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_course_home_page($course, null, null, null, null, $modnamesused);
}

// Include course format js module
$PAGE->requires->js('/course/format/sections/format.js');