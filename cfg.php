<?php
define('CONFIG', json_decode(file_get_contents('config.json'), TRUE));
const TOKEN = CONFIG['token'];

const START_MSG = CONFIG['start_msg'];
const START_BTNS = CONFIG['start_btns'];
const START_ROWS = CONFIG['start_rows'];

const SUPPORT_MSG = CONFIG['support_msg'];
const SUPPORT_BTN = CONFIG['support_btn'];
const SUPPORT_BTN_TEXT = CONFIG['support_btn_text'];
const SUPPORT_URL = CONFIG['support_url'];

const TELEGRAM_CHANNEL_MSG = CONFIG['telegram_channel_msg'];
const TELEGRAM_CHANNEL_BTN = CONFIG['telegram_channel_btn'];
const TELEGRAM_CHANNEL_BTN_TEXT = CONFIG['telegram_channel_btn_text'];
const TELEGRAM_CHANNEL_URL = CONFIG['telegram_channel_url'];

const TELEGRAM_CHAT_MSG = CONFIG['telegram_chat_msg'];
const TELEGRAM_CHAT_BTN = CONFIG['telegram_chat_btn'];
const TELEGRAM_CHAT_BTN_TEXT = CONFIG['telegram_chat_btn_text'];
const TELEGRAM_CHAT_URL = CONFIG['telegram_chat_url'];

const SERVICES_MSG = CONFIG['services_msg'];
const SERVICES_BTNS = CONFIG['services_btns'];
const SERVICES_BTN_TEXT = CONFIG['services_btn_text'];
const SERVICES_ROWS = CONFIG['services_rows'];
const SERVICES = CONFIG['services'];
const SERVICES_ENG = CONFIG['services_eng'];
const SERVICES_IDS = CONFIG['services_ids'];

const STOP_WATCHING_ADS = CONFIG['stop_watching_ads'];
