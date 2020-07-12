<?php
//  ------------------------------------------------------------------------ //
//                        Contact Plus Module for                            //
//               XOOPS - PHP Content Management System 2.0                   //
//                            Versión 1.2                                    //
//                   Copyright (c) 2002 Mario Figge                          //
//                       http://www.zona84.com                               //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
include_once "admin_header.php";
$table =  $xoopsDB->prefix("contactplus_elements");

$op = "options";

if (isset($HTTP_GET_VARS)) {
    foreach ($HTTP_GET_VARS as $k => $v) {
        $$k = $v;
    }
}

if (isset($HTTP_POST_VARS)) {
    foreach ($HTTP_POST_VARS as $k => $v) {
        $$k = $v;
    }
}


// Menú de opciones
if ($op == "options") {
    xoops_cp_header();
    echo  "<h4 style='text-align:left;'>"._CTP_CONTACTPLUS."</h4>";
    OpenTable();

    echo " - <a href=index.php?op=form_wizard>"._CTP_FORM_WIZARD."</a>";
    echo "<br><br>";
//    echo " - <a href='".XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$xoopsModule->getVar('mid')."'>"._CTP_GENERALCONF."</a>\n";

    CloseTable();
    xoops_cp_footer();
    exit();
}

if ($op == "form_wizard") {

    $myts =& MyTextSanitizer::getInstance();
    xoops_cp_header();
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

    // formulario generado de muestra
    echo "
    <h4 style='text-align:left;'>"._CTP_FORM_WIZARD."</h4>
    <h5 style='text-align:left;'>"._CTP_RESULT_FORM."</h5>
    <form action='index.php?op=reorder' method='post'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td class='bg2'>
    <table width='100%' border='0' cellpadding='4' cellspacing='1'>
    <tr class='bg3' align='center'><td align='left'>"._CTP_CAPTION."</td><td>"._CTP_ELEMENT."</td><td>"._CTP_REQUIRED."</td><td>"._CTP_ORDER."</td><td>&nbsp;</td></tr>";
    $sql = "SELECT * FROM $table ORDER BY ord";
    $result = $xoopsDB->query($sql);
    $count = 0;

    while ( list($id_element, $type, $caption, $name, $value, $parameter1, $parameter2, $ord, $req) = $xoopsDB->fetchRow($result) ) {
        switch ($type) {
            case 'textbox':
                  $element = new XoopsFormText($caption, $name, $parameter1, $parameter2, $value);
                  break;
            case 'textarea':
                  $element = new XoopsFormTextArea($caption, $name, $value, $parameter1, $parameter2 );
                  break;
        }
        $req = $req == 1 ? "<strong>"._YES."</strong>" : _NO;
        echo "<tr><td class='head'>".$caption."</td>";
        echo "<td class='head'>".$element->render()."</td>";
        echo "<td class='bg1' align='center'>".$req."</td>";
        echo "<td class='bg1'>
                <input type='hidden' value='$ord' name='old_ord[]' />
                <input type='hidden' value='$id_element' name='id[]' />
                <input type='text' value='$ord' name='new_ord[]' maxlength='2' size='2' />
                </td>";
        echo "<td class='bg1' nowrap='nowrap'><a href='$type.php?edit_id=$id_element'>"._EDIT."</a> | <a href='index.php?op=delete&id=$id_element'>"._DELETE."</a></td></tr>";
        $count++;
    }

    if ($count > 0) {
        echo "<tr align='center' class='bg3'><td colspan='5'><input type='submit' value='"._SUBMIT."' /><input type='hidden' name='op' value='edit' /></td></tr>";
    }
    echo "</table></td></tr></table></form>
    <br />";

    //elementos disponibles para añadir
    echo "<h5 style='text-align:left;'>"._CTP_ELEMENTS."</h5>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td class='bg2'>
    <table width='100%' border='0' cellpadding='4' cellspacing='1'>
    <tr class='bg3' align='center'><td>"._CTP_ELEMENT."</td><td>&nbsp;</td></tr>";

    echo "<tr class='bg1'>
           <td class='head'>"._CTP_TEXTBOX."<br />
           <span style='font-weight:normal;'>"._CTP_TEXTBOX_DESC."</span></td>
           <td><a href='textbox.php'>"._ADD."</a></td></tr>";
    echo "<tr class='bg1'>
           <td class='head'>"._CTP_TEXTAREA."<br />
           <span style='font-weight:normal;'>"._CTP_TEXTAREA_DESC."</span></td>
           <td><a href='textarea.php'>"._ADD."</a></td></tr>";

    echo "</table></td></tr></table>";
    xoops_cp_footer();
    exit();
}

if ($op == "delete") {
    // delete element
    if ($ok == 1) {
        $sql = "DELETE FROM $table WHERE id_element = ".$id ;
        if (!$xoopsDB->query($sql)) {
            xoops_cp_header();
            echo "Could not delete element";
            xoops_cp_footer();
        } else {
            redirect_header("index.php?op=form_wizard",1,_CPT_DBSUCCESS);
        }
        exit();
    } else {
        xoops_cp_header();
        xoops_confirm(array('op' => 'delete', 'id' => $id, 'ok' => 1), 'index.php', _CPT_SUREDEL);
        xoops_cp_footer();
        exit();
    }
}

if ($op == "reorder") {
    $count = count($new_ord);
    for ($i = 0; $i < $count; $i++) {
        if ( $new_ord[$i] != $old_ord[$i] ) {
            $sql = "UPDATE $table SET ord=".$new_ord[$i]." WHERE id_element=".$id[$i];
            echo $sql;
            $xoopsDB->query($sql);
        }
    }
    redirect_header("index.php?op=form_wizard",1,_CPT_DBSUCCESS);
    exit();
}

?>