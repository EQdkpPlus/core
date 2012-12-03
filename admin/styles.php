<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * styles.php
 * Began: Thu January 16 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Styles extends EQdkp_Admin
{
    var $style = array();

    function manage_styles()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $defaults = array(
            'attendees_columns' => 8,
            'date_notime_long'  => 'F j, Y',
            'date_notime_short' => 'm/d/y',
            'date_time'         => 'm/d/y h:ia T',
            'logo_path'         => 'logo.gif'
            );

        $this->style = array(
            'style_name'         => post_or_db('style_name'),
            'template_path'      => post_or_db('template_path'),
            'body_background'    => post_or_db('body_background'),
            'body_link'          => post_or_db('body_link'),
            'body_link_style'    => post_or_db('body_link_style'),
            'body_hlink'         => post_or_db('body_hlink'),
            'body_hlink_style'   => post_or_db('body_hlink_style'),
            'header_link'        => post_or_db('header_link'),
            'header_link_style'  => post_or_db('header_link_style'),
            'header_hlink'       => post_or_db('header_hlink'),
            'header_hlink_style' => post_or_db('header_hlink_style'),
            'tr_color1'          => post_or_db('tr_color1'),
            'tr_color2'          => post_or_db('tr_color2'),
            'th_color1'          => post_or_db('th_color1'),
            'fontface1'          => post_or_db('fontface1'),
            'fontface2'          => post_or_db('fontface2'),
            'fontface3'          => post_or_db('fontface3'),
            'fontsize1'          => post_or_db('fontsize1'),
            'fontsize2'          => post_or_db('fontsize2'),
            'fontsize3'          => post_or_db('fontsize3'),
            'fontcolor1'         => post_or_db('fontcolor1'),
            'fontcolor2'         => post_or_db('fontcolor2'),
            'fontcolor3'         => post_or_db('fontcolor3'),
            'fontcolor_neg'      => post_or_db('fontcolor_neg'),
            'fontcolor_pos'      => post_or_db('fontcolor_pos'),
            'table_border_width' => post_or_db('table_border_width'),
            'table_border_color' => post_or_db('table_border_color'),
            'table_border_style' => post_or_db('table_border_style'),
            'input_color'        => post_or_db('input_color'),
            'input_border_width' => post_or_db('input_border_width'),
            'input_border_color' => post_or_db('input_border_color'),
            'input_border_style' => post_or_db('input_border_style'),
            'attendees_columns'  => post_or_db('attendees_columns', $defaults),
            'date_notime_long'   => post_or_db('date_notime_long', $defaults),
            'date_notime_short'  => post_or_db('date_notime_short', $defaults),
            'date_time'          => post_or_db('date_time', $defaults),
            'logo_path'          => post_or_db('logo_path', $defaults)
        );

        // Vars used to confirm deletion
        $this->set_vars(array(
            'confirm_text'  => $user->lang['confirm_delete_style'],
            'uri_parameter' => 'styleid')
        );

        $this->assoc_buttons(array(
            'add' => array(
                'name'    => 'add',
                'process' => 'process_add',
                'check'   => 'a_styles_man'),
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_styles_man'),
            'delete' => array(
                'name'    => 'delete',
                'process' => 'process_delete',
                'check'   => 'a_styles_man'),
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_styles_man'))
        );

        $this->assoc_params(array(
            'create' => array(
                'name'    => 'mode',
                'value'   => 'create',
                'process' => 'display_form',
                'check'   => 'a_styles_man'),
            'edit' => array(
                'name'    => 'styleid',
                'process' => 'display_form',
                'check'   => 'a_styles_man'))
        );

        // Build the style array
        // ---------------------------------------------------------
        if ( $this->url_id )
        {
            $sql = 'SELECT s.*, c.*
                    FROM ' . STYLES_TABLE . ' s, ' . STYLES_CONFIG_TABLE . " c
                    WHERE (s.style_id = c.style_id)
                    AND s.style_id='" . $this->url_id . "'";
            $result = $db->query($sql);
            if ( !$row = $db->fetch_record($result) )
            {
                message_die($user->lang['error_invalid_style']);
            }
            $db->free_result($result);

            $this->style = array(
                'style_name'         => post_or_db('style_name', $row),
                'template_path'      => post_or_db('template_path', $row),
                'body_background'    => post_or_db('body_background', $row),
                'body_link'          => post_or_db('body_link', $row),
                'body_link_style'    => post_or_db('body_link_style', $row),
                'body_hlink'         => post_or_db('body_hlink', $row),
                'body_hlink_style'   => post_or_db('body_hlink_style', $row),
                'header_link'        => post_or_db('header_link', $row),
                'header_link_style'  => post_or_db('header_link_style', $row),
                'header_hlink'       => post_or_db('header_hlink', $row),
                'header_hlink_style' => post_or_db('header_hlink_style', $row),
                'tr_color1'          => post_or_db('tr_color1', $row),
                'tr_color2'          => post_or_db('tr_color2', $row),
                'th_color1'          => post_or_db('th_color1', $row),
                'fontface1'          => post_or_db('fontface1', $row),
                'fontface2'          => post_or_db('fontface2', $row),
                'fontface3'          => post_or_db('fontface3', $row),
                'fontsize1'          => post_or_db('fontsize1', $row),
                'fontsize2'          => post_or_db('fontsize2', $row),
                'fontsize3'          => post_or_db('fontsize3', $row),
                'fontcolor1'         => post_or_db('fontcolor1', $row),
                'fontcolor2'         => post_or_db('fontcolor2', $row),
                'fontcolor3'         => post_or_db('fontcolor3', $row),
                'fontcolor_neg'      => post_or_db('fontcolor_neg', $row),
                'fontcolor_pos'      => post_or_db('fontcolor_pos', $row),
                'table_border_width' => post_or_db('table_border_width', $row),
                'table_border_color' => post_or_db('table_border_color', $row),
                'table_border_style' => post_or_db('table_border_style', $row),
                'input_color'        => post_or_db('input_color', $row),
                'input_border_width' => post_or_db('input_border_width', $row),
                'input_border_color' => post_or_db('input_border_color', $row),
                'input_border_style' => post_or_db('input_border_style', $row),
                'attendees_columns'  => post_or_db('attendees_columns', $row),
                'date_notime_long'   => post_or_db('date_notime_long', $row),
                'date_notime_short'  => post_or_db('date_notime_short', $row),
                'date_time'          => post_or_db('date_time', $row),
                'logo_path'          => post_or_db('logo_path', $row)
            );
        }
    }

    function error_check()
    {
        return false;
    }

    // ---------------------------------------------------------
    // Process Add
    // ---------------------------------------------------------
    function process_add()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        extract($_POST);
        $query = $db->build_query('INSERT', array(
            'style_name'         => $style_name,
            'template_path'      => $template_path,
            'body_background'    => $body_background,
            'body_link'          => $body_link,
            'body_link_style'    => $body_link_style,
            'body_hlink'         => $body_hlink,
            'body_hlink_style'   => $body_hlink_style,
            'header_link'        => $header_link,
            'header_link_style'  => $header_link_style,
            'header_hlink'       => $header_hlink,
            'header_hlink_style' => $header_hlink_style,
            'tr_color1'          => $tr_color1,
            'tr_color2'          => $tr_color2,
            'th_color1'          => $th_color1,
            'fontface1'          => $fontface1,
            'fontface2'          => $fontface2,
            'fontface3'          => $fontface3,
            'fontsize1'          => $fontsize1,
            'fontsize2'          => $fontsize2,
            'fontsize3'          => $fontsize3,
            'fontcolor1'         => $fontcolor1,
            'fontcolor2'         => $fontcolor2,
            'fontcolor3'         => $fontcolor3,
            'fontcolor_neg'      => $fontcolor_neg,
            'fontcolor_pos'      => $fontcolor_pos,
            'table_border_width' => $table_border_width,
            'table_border_color' => $table_border_color,
            'table_border_style' => $table_border_style,
            'input_color'        => $input_color,
            'input_border_width' => $input_border_width,
            'input_border_color' => $input_border_color,
            'input_border_style' => $input_border_style
            )
        );
        $db->query('INSERT INTO ' . STYLES_TABLE . $query);
        $style_id = $db->insert_id();

        $query = $db->build_query('INSERT', array(
            'style_id'          => $style_id,
            'attendees_columns' => $attendees_columns,
            'date_notime_long'  => $date_notime_long,
            'date_notime_short' => $date_notime_short,
            'date_time'         => $date_time,
            'logo_path'         => $logo_path)
        );
        $db->query('INSERT INTO ' . STYLES_CONFIG_TABLE . $query);

        message_die($user->lang['admin_add_style_success']);
    }

    // ---------------------------------------------------------
    // Process Update
    // ---------------------------------------------------------
    function process_update()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        extract($_POST);
        $query = $db->build_query('UPDATE', array(
            'style_name'         => $style_name,
            'template_path'      => $template_path,
            'body_background'    => $body_background,
            'body_link'          => $body_link,
            'body_link_style'    => $body_link_style,
            'body_hlink'         => $body_hlink,
            'body_hlink_style'   => $body_hlink_style,
            'header_link'        => $header_link,
            'header_link_style'  => $header_link_style,
            'header_hlink'       => $header_hlink,
            'header_hlink_style' => $header_hlink_style,
            'tr_color1'          => $tr_color1,
            'tr_color2'          => $tr_color2,
            'th_color1'          => $th_color1,
            'fontface1'          => $fontface1,
            'fontface2'          => $fontface2,
            'fontface3'          => $fontface3,
            'fontsize1'          => $fontsize1,
            'fontsize2'          => $fontsize2,
            'fontsize3'          => $fontsize3,
            'fontcolor1'         => $fontcolor1,
            'fontcolor2'         => $fontcolor2,
            'fontcolor3'         => $fontcolor3,
            'fontcolor_neg'      => $fontcolor_neg,
            'fontcolor_pos'      => $fontcolor_pos,
            'table_border_width' => $table_border_width,
            'table_border_color' => $table_border_color,
            'table_border_style' => $table_border_style,
            'input_color'        => $input_color,
            'input_border_width' => $input_border_width,
            'input_border_color' => $input_border_color,
            'input_border_style' => $input_border_style
            )
        );
        $db->query('UPDATE ' . STYLES_TABLE . ' SET ' . $query . " WHERE style_id='" . $this->url_id . "'");

        $query = $db->build_query('UPDATE', array(
            'attendees_columns' => $attendees_columns,
            'date_notime_long'  => $date_notime_long,
            'date_notime_short' => $date_notime_short,
            'date_time'         => $date_time,
            'logo_path'         => $logo_path)
        );
        $db->query('UPDATE ' . STYLES_CONFIG_TABLE . ' SET ' . $query . " WHERE style_id='" . $this->url_id . "'");

        message_die($user->lang['admin_update_style_success']);
    }

    // ---------------------------------------------------------
    // Process Delete (confirmed)
    // ---------------------------------------------------------
    function process_confirm()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $db->query('DELETE FROM ' . STYLES_TABLE        . " WHERE style_id='" . $this->url_id . "'");
        $db->query('DELETE FROM ' . STYLES_CONFIG_TABLE . " WHERE style_id='" . $this->url_id . "'");

        message_die($user->lang['admin_delete_style_success']);
    }

    // ---------------------------------------------------------
    // Process helper methods
    // ---------------------------------------------------------

    // ---------------------------------------------------------
    // Display
    // ---------------------------------------------------------
    function display_list()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $sql = 'SELECT style_id, style_name, template_path, count(u.user_id) AS users
                FROM (' . STYLES_TABLE . ' s
                LEFT JOIN ' . USERS_TABLE . ' u
                ON u.user_style = s.style_id)
                GROUP BY s.style_id
                ORDER BY s.style_name';
        $result = $db->query($sql);
        while ( $row = $db->fetch_record($result) )
        {
            $tpl->assign_block_vars('styles_row', array(
                'ROW_CLASS'    => $eqdkp->switch_row_class(),
                'U_EDIT_STYLE' => 'styles.php' . $SID . '&amp;styleid=' . $row['style_id'],
                'NAME'         => stripslashes($row['style_name']),
                'TEMPLATE'     => $row['template_path'],
                'USERS'        => $row['users'],
                'U_PREVIEW'    => 'styles.php' . $SID . '&amp;style=' . $row['style_id'])
            );
        }
        $db->free_result($result);

        $tpl->assign_vars(array(
            'L_NAME'     => $user->lang['name'],
            'L_TEMPLATE' => $user->lang['template'],
            'L_USERS'    => $user->lang['users'],
            'L_PREVIEW'  => $user->lang['preview'])
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['styles_title'],
            'template_file' => 'admin/styles.html',
            'display'       => true)
        );
    }

    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $text_decoration = array(
            'none',
            'underline',
            'overline',
            'line-through',
            'blink');
        $border_style = array(
            'none',
            'hidden',
            'dotted',
            'dashed',
            'solid',
            'double',
            'groove',
            'ridge',
            'inset',
            'outset');

        //
        // Available templates
        //
        if ( $dir = @opendir($eqdkp->root_path . 'templates/') )
        {
            while ( $file = @readdir($dir) )
            {
                if ( (!is_file($eqdkp->root_path . 'templates/' . $file)) && (!is_link($eqdkp->root_path . 'templates/' . $file)) && valid_folder($file) )
                {
                    $tpl->assign_block_vars('template_row', array(
                        'VALUE'    => stripslashes($file),
                        'SELECTED' => ( $this->style['template_path'] == stripslashes($file) ) ? ' selected="selected"' : '',
                        'OPTION'   => stripslashes($file))
                    );
                }
            }
        }

        //
        // Text decorations
        //
        foreach ( $text_decoration as $k => $v )
        {
            $tpl->assign_block_vars('body_link_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['body_link_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
            $tpl->assign_block_vars('body_hlink_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['body_hlink_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
            $tpl->assign_block_vars('header_link_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['header_link_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
            $tpl->assign_block_vars('header_hlink_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['header_hlink_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
        }

        //
        // Border styles
        //
        foreach ( $border_style as $k => $v )
        {
            $tpl->assign_block_vars('table_border_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['table_border_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
            $tpl->assign_block_vars('input_border_style_row', array(
                'VALUE'    => $v,
                'SELECTED' => ( $this->style['input_border_style'] == $v ) ? ' selected="selected"' : '',
                'OPTION'   => $v)
            );
        }

        //
        // Attendees columns
        //
        for ( $i = 1; $i < 11; $i++)
        {
            $tpl->assign_block_vars('attendees_columns_row', array(
                'VALUE'    => $i,
                'SELECTED' => ( $this->style['attendees_columns'] == $i ) ? ' selected="selected"' : '',
                'OPTION'   => $i)
            );
        }

        $tpl->assign_vars(array(
            // Form vars
            'F_ADD_STYLE' => 'styles.php' . $SID,
            'STYLE_ID'    => $this->url_id,

            // Form Values
            'STYLE_NAME'         => $this->style['style_name'],
            'BODY_BACKGROUND'    => $this->style['body_background'],
            'BODY_LINK'          => $this->style['body_link'],
            'BODY_HLINK'         => $this->style['body_hlink'],
            'HEADER_LINK'        => $this->style['header_link'],
            'HEADER_HLINK'       => $this->style['header_hlink'],
            'TR_COLOR1'          => $this->style['tr_color1'],
            'TR_COLOR2'          => $this->style['tr_color2'],
            'TH_COLOR1'          => $this->style['th_color1'],
            'FONTFACE1'          => $this->style['fontface1'],
            'FONTFACE2'          => $this->style['fontface2'],
            'FONTFACE3'          => $this->style['fontface3'],
            'FONTSIZE1'          => $this->style['fontsize1'],
            'FONTSIZE2'          => $this->style['fontsize2'],
            'FONTSIZE3'          => $this->style['fontsize3'],
            'FONTCOLOR1'         => $this->style['fontcolor1'],
            'FONTCOLOR2'         => $this->style['fontcolor2'],
            'FONTCOLOR3'         => $this->style['fontcolor3'],
            'FONTCOLOR_NEG'      => $this->style['fontcolor_neg'],
            'FONTCOLOR_POS'      => $this->style['fontcolor_pos'],
            'TABLE_BORDER_WIDTH' => $this->style['table_border_width'],
            'TABLE_BORDER_COLOR' => $this->style['table_border_color'],
            'TABLE_BORDER_STYLE' => $this->style['table_border_style'],
            'INPUT_COLOR'        => $this->style['input_color'],
            'INPUT_BORDER_WIDTH' => $this->style['input_border_width'],
            'INPUT_BORDER_COLOR' => $this->style['input_border_color'],
            'INPUT_BORDER_STYLE' => $this->style['input_border_style'],
            'DATE_NOTIME_LONG'   => $this->style['date_notime_long'],
            'DATE_NOTIME_SHORT'  => $this->style['date_notime_short'],
            'DATE_TIME'          => $this->style['date_time'],
            'STYLE_LOGO_PATH'    => $this->style['logo_path'],
            // Language
            'L_STYLE_SETTINGS'         => $user->lang['style_settings'],
            'L_STYLE_NAME'             => $user->lang['style_name'],
            'L_TEMPLATE'               => $user->lang['template'],
            'L_ELEMENT'                => $user->lang['element'],
            'L_VALUE'                  => $user->lang['value'],
            'L_BACKGROUND_COLOR'       => $user->lang['background_color'],
            'L_FONTFACE1'              => $user->lang['fontface1'],
            'L_FONTFACE1_NOTE'         => $user->lang['fontface1_note'],
            'L_FONTFACE2'              => $user->lang['fontface2'],
            'L_FONTFACE2_NOTE'         => $user->lang['fontface2_note'],
            'L_FONTFACE3'              => $user->lang['fontface3'],
            'L_FONTFACE3_NOTE'         => $user->lang['fontface3_note'],
            'L_FONTSIZE1'              => $user->lang['fontsize1'],
            'L_FONTSIZE1_NOTE'         => $user->lang['fontsize1_note'],
            'L_FONTSIZE2'              => $user->lang['fontsize2'],
            'L_FONTSIZE2_NOTE'         => $user->lang['fontsize2_note'],
            'L_FONTSIZE3'              => $user->lang['fontsize3'],
            'L_FONTSIZE3_NOTE'         => $user->lang['fontsize3_note'],
            'L_FONTCOLOR1'             => $user->lang['fontcolor1'],
            'L_FONTCOLOR1_NOTE'        => $user->lang['fontcolor1_note'],
            'L_FONTCOLOR2'             => $user->lang['fontcolor2'],
            'L_FONTCOLOR2_NOTE'        => $user->lang['fontcolor2_note'],
            'L_FONTCOLOR3'             => $user->lang['fontcolor3'],
            'L_FONTCOLOR3_NOTE'        => $user->lang['fontcolor3_note'],
            'L_FONTCOLOR_NEG'          => $user->lang['fontcolor_neg'],
            'L_FONTCOLOR_NEG_NOTE'     => $user->lang['fontcolor_neg_note'],
            'L_FONTCOLOR_POS'          => $user->lang['fontcolor_pos'],
            'L_FONTCOLOR_POS_NOTE'     => $user->lang['fontcolor_pos_note'],
            'L_BODY_LINK'              => $user->lang['body_link'],
            'L_BODY_LINK_STYLE'        => $user->lang['body_link_style'],
            'L_BODY_HLINK'             => $user->lang['body_hlink'],
            'L_BODY_HLINK_STYLE'       => $user->lang['body_hlink_style'],
            'L_HEADER_LINK'            => $user->lang['header_link'],
            'L_HEADER_LINK_STYLE'      => $user->lang['header_link_style'],
            'L_HEADER_HLINK'           => $user->lang['header_hlink'],
            'L_HEADER_HLINK_STYLE'     => $user->lang['header_hlink_style'],
            'L_TR_COLOR1'              => $user->lang['tr_color1'],
            'L_TR_COLOR2'              => $user->lang['tr_color2'],
            'L_TH_COLOR1'              => $user->lang['th_color1'],
            'L_TABLE_BORDER_WIDTH'     => $user->lang['table_border_width'],
            'L_TABLE_BORDER_COLOR'     => $user->lang['table_border_color'],
            'L_TABLE_BORDER_STYLE'     => $user->lang['table_border_style'],
            'L_INPUT_COLOR'            => $user->lang['input_color'],
            'L_INPUT_BORDER_WIDTH'     => $user->lang['input_border_width'],
            'L_INPUT_BORDER_COLOR'     => $user->lang['input_border_color'],
            'L_INPUT_BORDER_STYLE'     => $user->lang['input_border_style'],
            'L_STYLE_CONFIGURATION'    => $user->lang['style_configuration'],
            'L_STYLE_DATE_NOTE'        => $user->lang['style_date_note'],
            'L_ATTENDEES_COLUMNS'      => $user->lang['attendees_columns'],
            'L_ATTENDEES_COLUMNS_NOTE' => $user->lang['attendees_columns_note'],
            'L_DATE_NOTIME_LONG'       => $user->lang['date_notime_long'],
            'L_DATE_NOTIME_SHORT'      => $user->lang['date_notime_short'],
            'L_DATE_TIME'              => $user->lang['date_time'],
            'L_LOGO_PATH'              => $user->lang['logo_path'],
            'L_ADD_STYLE'              => $user->lang['add_style'],
            'L_RESET'                  => $user->lang['reset'],
            'L_UPDATE_STYLE'           => $user->lang['update_style'],
            'L_DELETE_STYLE'           => $user->lang['delete_style'],

            // Buttons
            'S_ADD' => ( !$this->url_id ) ? true : false)
        );

        $eqdkp->set_vars(array(
            'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['styles_title'],
            'template_file' => 'admin/addstyle.html',
            'display'       => true)
        );
    }
}

$manage_styles = new Manage_Styles;
$manage_styles->process();
?>


