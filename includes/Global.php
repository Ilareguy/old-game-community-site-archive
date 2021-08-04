<?php

/*
 * Settings pour les notifications.
 * Chaque String correspond à une cellule dans la BD.
 */
$NOTIFY_ON_PM = 'NotifyOnPM';
$NOTIFY_ON_NEWS_POST = 'NotifyOnNewsPost';
$NOTIFY_ON_TOWN_JOIN = 'NotifyOnTownJoin';

/*
 * Sexes
 */
$GENDER_MALE = 1;
$GENDER_FEMALE = 0;

/*
 * Actions pour les notifications
 */
$NOTIFICATION_ACTION_MARK_PM_AS_READ = 0;
$NOTIFICATION_ACTION_DELETE_PM = 1;

/*
 * Facebook settings
 */
global $facebook;
$API_KEY = '313151885456652';                   // ApiKey/appId pour l'app Facebook
$SECRET = '0f7e5cc693401a7a514e999bc52788bf';   // Secret!
$ACCESS_TOKEN = '';                             // L'AccessToken du compte connecté
$ACCOUNT_LINKED_TO_FACEBOOK = false;            // Bool: si le compte CONNECTÉ a lié son compte Facebook ou non
?>