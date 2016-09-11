<?php

defined('MOODLE_INTERNAL') || die;
$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig) {
      $settings = new admin_settingpage('local_alexandriaimporter', get_string('pluginname', 'local_alexandriaimporter'));
      $ADMIN->add('localplugins', $settings);

      $settings->add(new admin_setting_configtext('local_alexandriaimporter/alexandria_url',
        get_string('alexandria_url', 'local_alexandriaimporter'), "", "http://alexandria.xtec.cat", PARAM_URL));

      $settings->add(new admin_setting_configtext('local_alexandriaimporter/alexandria_token',
        get_string('alexandria_token', 'local_alexandriaimporter'), get_string('alexandria_tokendesc', 'local_alexandriaimporter'),
        "", PARAM_TEXT));
}
