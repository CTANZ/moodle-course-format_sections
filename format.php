<?php

//
// Display the whole course as "sections" made of of modules
// This is based on "topics" format, defaults to showing 1 section at a time.

defined('MOODLE_INTERNAL') || die();

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

// Remember page for the duration of the session.
$home = optional_param('home', 0, PARAM_INT);
if (!empty($home) || !isset($_SESSION['current_topic_'.$course->id])) {
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
    $renderer->print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
} elseif (!empty($displaysection)) {
    $renderer->print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
} else {
    $renderer->print_course_home_page($course, $sections, $mods, $modnames, $modnamesused);
}