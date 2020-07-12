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
if ( $save == 'true') {
      //salvar y volver
    //extract($HTTP_POST_VARS);
    $myts =& MyTextSanitizer::getInstance();
    $type = "textarea";
    $caption = $myts->stripSlashesGPC($caption);
    $name = str_replace(" ", "_",strtolower($caption));
    $parameter1 = $myts->stripSlashesGPC($rows);
    $parameter2 = $myts->stripSlashesGPC($cols);
    $value = $myts->stripSlashesGPC($value);
    $req = !empty($req) ? 1 : 0;

    if (empty($save_id)) {
    $sql = "INSERT INTO $table (type, caption, name, value, parameter1, parameter2, req)
                        VALUES ('$type', '$caption', '$name', '$value', '$parameter1', '$parameter2', $req)";
    }
    else {
    $sql = "UPDATE $table SET type='$type', caption='$caption', name='$name', value='$value', parameter1='$parameter1', parameter2='$parameter2', req=$req WHERE id_element = $save_id";
    }

    if (!$xoopsDB->query($sql)) {
        xoops_cp_header();
        echo $sql." Could not add element";
        xoops_cp_footer();
    } else {
        redirect_header("index.php?op=form_wizard",1,_CPT_DBSUCCESS);
    }
    exit();


}

if ( !empty($edit_id) ) {
      // editar elemento
      $sql = "SELECT * FROM $table WHERE id_element = $edit_id";
      $result = $xoopsDB->query($sql);
      list($id_element, $type, $caption, $name, $value, $rows, $cols, $ord, $req) = $xoopsDB->fetchRow($result);
}

      //valores por defecto
      if (empty($rows)) $rows = 5;
      if (empty($cols)) $cols = 50;

      // formulario de opciones para el elemento
      xoops_cp_header();

      echo "<h4 style='text-align:left;'>"._CTP_FORM_WIZARD."</h4>
            <form action='textarea.php?save_id=$edit_id' method='post'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td class='bg2'>
            <table width='100%' border='0' cellpadding='4' cellspacing='1'>
            <tr class='bg3' align='center'><td>"._CTP_OPTIONS."</td><td>"._CTP_VALUE."</td></tr>";

      echo "<tr class='bg1'>
             <td class='head'>Caption</td>
             <td><input name='caption' type='text' value='$caption' /></td>
            </tr>
            <tr class='bg1'>
              <td class='head'>Rows</td>
              <td><input name='rows' type='text' value='$rows' /></td>
            </tr>
            <tr class='bg1'>
              <td class='head'>Cols</td>
              <td><input name='cols' type='text' value='$cols' /></td>
            </tr>
            <tr class='bg1'>
              <td class='head'>Initial text</td>
              <td><textarea name='value' cols='50' rows='5'>$value</textarea></td>
            </tr>
            <tr class='bg1'>
              <td class='head'>Required</td>
              <td><input name='req' type='checkbox' value='true' ";
              if ($req == 1) echo " checked='checked' ";
              echo "/></td>
            </tr>
            <tr class='bg3'>
              <td colspan='2'>
              <input type='submit' value='"._SUBMIT."' />
              <input name=''Button' type='button'' onclick='javascript:history.go(-1);' value='"._CANCEL."' />
              <input type='hidden' name='save' value='true' /></td>
            </tr>";
      echo "</table></td></tr></table>";
      echo "</form>";
    xoops_cp_footer();
    exit();
?>