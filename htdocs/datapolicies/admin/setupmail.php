<?php

/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018      Nicolas ZABOURI      <info@inovea-conseil.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    datapolicies/admin/setupmail.php
 * \ingroup datapolicies
 * \brief   datapolicies setup page.
 */
// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"]))
    $res = @include($_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--;
    $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php"))
    $res = @include(substr($tmp, 0, ($i + 1)) . "/main.inc.php");
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php"))
    $res = @include(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php");
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php"))
    $res = @include("../../main.inc.php");
if (!$res && file_exists("../../../main.inc.php"))
    $res = @include("../../../main.inc.php");
if (!$res)
    die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . '/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formadmin.class.php';
require_once '../lib/datapolicies.lib.php';

//require_once "../class/myclass.class.php";
// Translations
$langs->load('admin');
$langs->load('companies');
$langs->load('members');
$langs->load('datapolicies@datapolicies');


// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');
$formadmin = new FormAdmin($db);

if (GETPOST('l')) {
    $l = GETPOST('l');
} else {
    $l = $langs->defaultlang;
}
// Access control
if (!$user->admin)
    accessforbidden();

/*
 * Actions
 */

include DOL_DOCUMENT_ROOT . '/core/actions_setmoduleoptions.inc.php';

if ($action == 'setvalue' && $user->admin) {
    $db->begin();
    $sub = "DATAPOLICIESSUBJECT_" . $l;
    $result = dolibarr_set_const($db, $sub, GETPOST($sub), 'chaine', 0, '', $conf->entity);
    $cont = "DATAPOLICIESCONTENT_" . $l;
    $result = dolibarr_set_const($db, $cont, GETPOST($cont), 'chaine', 0, '', $conf->entity);
    $cont = "TXTLINKDATAPOLICIESACCEPT_" . $l;
    $result = dolibarr_set_const($db, $cont, GETPOST($cont), 'chaine', 0, '', $conf->entity);
    $cont = "TXTLINKDATAPOLICIESREFUSE_" . $l;
    $result = dolibarr_set_const($db, $cont, GETPOST($cont), 'chaine', 0, '', $conf->entity);
    $sub = "DATAPOLICIESACCEPT_" . $l;
    $result = dolibarr_set_const($db, $sub, GETPOST($sub), 'chaine', 0, '', $conf->entity);
    $sub = "DATAPOLICIESREFUSE_" . $l;
    $result = dolibarr_set_const($db, $sub, GETPOST($sub), 'chaine', 0, '', $conf->entity);
    if (!$result > 0)
        $error++;
    if (!$error) {
        $db->commit();
        setEventMessage($langs->trans("SetupSaved"));
    } else {
        $db->rollback();
        dol_print_error($db);
    }
}


/*
 * View
 */

$page_name = "datapoliciesSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . ($backtopage ? $backtopage : DOL_URL_ROOT . '/admin/modules.php?restore_lastsearch_values=1') . '">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_datapolicies@datapolicies');

// Configuration header
$head = datapoliciesAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "datapolicies@datapolicies");





print "<script type='text/javascript'>
        $(document).ready(function(){
         $('#default_lang').change(function(){
         lang=$('#default_lang').val();
                    window.location.replace('" . $_SERVER['PHP_SELF'] . "?l='+lang);
                    });
        });
</script>";

print '<form method="post" action="' . $_SERVER["PHP_SELF"] . '?l=' . $l . '">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="action" value="setvalue">';
print '<table>';
if ($conf->global->MAIN_MULTILANGS) {
    print '<tr><td>' . fieldLabel('DefaultLang', 'default_lang') . '</td><td colspan="3" class="maxwidthonsmartphone">' . "\n";
    print $formadmin->select_language((GETPOST('l') ? GETPOST('l') : $langs->defaultlang), 'default_lang', 0, 0, 1, 0, 0, 'maxwidth200onsmartphone');
    print '</tr>';
}
$subject = 'DATAPOLICIESSUBJECT_' . $l;
$linka = 'TXTLINKDATAPOLICIESACCEPT_' . $l;
$linkr = 'TXTLINKDATAPOLICIESREFUSE_' . $l;
$content = 'DATAPOLICIESCONTENT_' . $l;
$acc = 'DATAPOLICIESACCEPT_' . $l;
$ref = 'DATAPOLICIESREFUSE_' . $l;
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans('DATAPOLICIESSUBJECTMAIL') . '</td><td>';
print '<input type="text" size="100" name="' . $subject . '" value="' . $conf->global->$subject . '" />';
print '</td><tr>';
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans('DATAPOLICIESCONTENTMAIL').'</td><td>';
print $langs->trans('DATAPOLICIESSUBSITUTION');echo'__LINKACCEPT__,__LINKREFUSED__,__FIRSTNAME__,__NAME__,__CIVILITY__';
$doleditor = new DolEditor($content, $conf->global->$content, '', 250, 'Full', '', false, true, 1, 200, 70);
$doleditor->Create();
print '</td><tr>';
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans('TXTLINKDATAPOLICIESACCEPT') . '</td><td>';
print '<input type="text" size="200" name="' . $linka . '" value="' . $conf->global->$linka . '" />';
print '</td><tr>';
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans('TXTLINKDATAPOLICIESREFUSE') . '</td><td>';
print '<input type="text" size="200" name="' . $linkr . '" value="' . $conf->global->$linkr . '" />';
print '</td><tr>';
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';

print $langs->trans('DATAPOLICIESACCEPT').'</td><td>';

$doleditor = new DolEditor($acc, $conf->global->$acc, '', 250, 'Full', '', false, true, 1, 200, 70);
$doleditor->Create();
print '</td><tr>';
print '<tr ' . $bc[$var] . '><td class="fieldrequired">';
print $langs->trans('DATAPOLICIESREFUSE').'</td><td>';

print $langs->trans('');
$doleditor = new DolEditor($ref, $conf->global->$ref, '', 250, 'Full', '', false, true, 1, 200, 70);
$doleditor->Create();
print '</td><tr>';
print '</table>';

print '<br><center><input type="submit" class="button" value="' . $langs->trans("Modify") . '"></center>';

print '</form>';

dol_fiche_end();

print '<br><br>';

print $langs->trans('SendAgreementText');
print '<a class="button" href="'.dol_buildpath('/datapolicies/mailing.php').'">'.$langs->trans('SendAgreement').'</a>';

llxFooter();
$db->close();