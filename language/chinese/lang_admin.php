<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_admin.php
 * Began: Fri January 3 2003
 *
 * $Id$
 *
 *
 * Chinese Simplified translated by 雪夜之狼@Feathermoon（羽月）,CN3
 * Email:xionglingfeng@Gmail.com
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

// Titles
$lang['addadj_title']         = '添加一个整体调整';
$lang['addevent_title']       = '添加一个事件';
$lang['addiadj_title']        = '添加一个个人调整';
$lang['additem_title']        = '添加一个物品购买';
$lang['addmember_title']      = '添加一个公会成员';
$lang['addnews_title']        = '添加一个新闻条目';
$lang['addraid_title']        = '添加一个 Raid';
$lang['addturnin_title']      = "添加一个上交 - 步骤 %1\$d";
$lang['admin_index_title']    = 'EQdkp 管理';
$lang['config_title']         = '脚本设置';
$lang['manage_members_title'] = '管理公会成员';
$lang['manage_users_title']   = '用户账号和许可';
$lang['parselog_title']       = '处理一个日志文件';
$lang['plugins_title']        = '管理插件';
$lang['styles_title']         = '管理样式';
$lang['viewlogs_title']       = '日志查看器';

// Page Foot Counts
$lang['listusers_footcount']             = "... 找到 %1\$d 用户 / %2\$d 每页";
$lang['manage_members_footcount']        = "... 找到 %1\$d 成员";
$lang['online_footcount']                = "... %1\$d 用户在线";
$lang['viewlogs_footcount']              = "... 找到 %1\$d 日志 / %2\$d 每页";

// Submit Buttons
$lang['add_adjustment'] = '添加调整';
$lang['add_account'] = '添加账号';
$lang['add_event'] = '添加事件';
$lang['add_item'] = '添加物品';
$lang['add_member'] = '添加成员';
$lang['add_news'] = '添加新闻';
$lang['add_raid'] = '添加 Raid';
$lang['add_style'] = '添加样式';
$lang['add_turnin'] = '添加上交';
$lang['delete_adjustment'] = '删除调整';
$lang['delete_event'] = '删除事件';
$lang['delete_item'] = '删除物品';
$lang['delete_member'] = '删除成员';
$lang['delete_news'] = '删除新闻';
$lang['delete_raid'] = '删除 Raid';
$lang['delete_selected_members'] = '删除已选择的成员';
$lang['delete_style'] = '删除样式';
$lang['mass_delete'] = '批量删除';
$lang['mass_update'] = '批量更新';
$lang['parse_log'] = '处理日志';
$lang['search_existing'] = '搜索存在的';
$lang['select'] = '选择';
$lang['transfer_history'] = '转移历史';
$lang['update_adjustment'] = '更新调整';
$lang['update_event'] = '更新事件';
$lang['update_item'] = '更新物品';
$lang['update_member'] = '更新成员r';
$lang['update_news'] = '更新新闻';
$lang['update_raid'] = '更新 Raid';
$lang['update_style'] = '更新样式';

// Misc
$lang['account_enabled'] = '账号已启用';
$lang['adjustment_value'] = '调整值';
$lang['adjustment_value_note'] = '可能为负';
$lang['code'] = '代码';
$lang['contact'] = '联系';
$lang['create'] = '创建';
$lang['found_members'] = "已处理 %1\$d 行， 找到 %2\$d 成员";
$lang['headline'] = '首行';
$lang['hide'] = '隐藏?';
$lang['install'] = '安装';
$lang['item_search'] = '物品搜索';
$lang['list_prefix'] = '列表前缀';
$lang['list_suffix'] = '列表后缀';
$lang['logs'] = '日志';
$lang['log_find_all'] = '查找全部 (包含匿名)';
$lang['manage_members'] = '管理成员';
$lang['manage_plugins'] = '管理插件';
$lang['manage_users'] = '管理用户';
$lang['mass_update_note'] = '如果你想把更改应用到所有上面选中的物品，使用这些控制并更改它们的属性并点击 "批量更新"。
                             删除选中的账号，仅仅点击 "批量删除"。';
$lang['members'] = '成员';
$lang['member_rank'] = '成员头衔';
$lang['message_body'] = '消息体';
$lang['message_show_loot_raid'] = '显示捡取来自 Raid:';
$lang['results'] = "%1\$d 结果 (\"%2\$s\")";
$lang['search'] = '搜索';
$lang['search_members'] = '搜索成员';
$lang['should_be'] = '应当';
$lang['styles'] = '样式';
$lang['title'] = '标题';
$lang['uninstall'] = '卸载';
$lang['enable']		= '启用';
$lang['update_date_to'] = "更新数据到<br />%1\$s?";
$lang['version'] = '版本';
$lang['x_members_s'] = "%1\$d 成员";
$lang['x_members_p'] = "%1\$d 成员";

// Permission Messages
$lang['noauth_a_event_add']    = '你没有许可添加事件。';
$lang['noauth_a_event_upd']    = '你没有许可更新事件。';
$lang['noauth_a_event_del']    = '你没有许可删除事件。';
$lang['noauth_a_groupadj_add'] = '你没有许可添加整体调整。';
$lang['noauth_a_groupadj_upd'] = '你没有许可更新整体调整。';
$lang['noauth_a_groupadj_del'] = '你没有许可删除整体调整。';
$lang['noauth_a_indivadj_add'] = '你没有许可添加个人调整。';
$lang['noauth_a_indivadj_upd'] = '你没有许可更新个人调整。';
$lang['noauth_a_indivadj_del'] = '你没有许可删除个人调整。';
$lang['noauth_a_item_add']     = '你没有许可添加物品。';
$lang['noauth_a_item_upd']     = '你没有许可更新物品。';
$lang['noauth_a_item_del']     = '你没有许可删除物品。';
$lang['noauth_a_news_add']     = '你没有许可添加新闻条目。';
$lang['noauth_a_news_upd']     = '你没有许可更新新闻条目。';
$lang['noauth_a_news_del']     = '你没有许可删除新闻条目。';
$lang['noauth_a_raid_add']     = '你没有许可添加 raids。';
$lang['noauth_a_raid_upd']     = '你没有许可更新 raids。';
$lang['noauth_a_raid_del']     = '你没有许可删除 raids。';
$lang['noauth_a_turnin_add']   = '你没有许可添加上交。';
$lang['noauth_a_config_man']   = '你没有许可管理 EQdkp 选项设置。';
$lang['noauth_a_members_man']  = '你没有许可管理公会成员。';
$lang['noauth_a_plugins_man']  = '你没有许可管理 EQdkp 插件。';
$lang['noauth_a_styles_man']   = '你没有许可管理 EQdkp 样式。';
$lang['noauth_a_users_man']    = '你没有许可管理用户设置。';
$lang['noauth_a_logs_view']    = '你没有许可查看 EQdkp 日志。';

// Submission Success Messages
$lang['admin_add_adj_success']               = "一个 %1\$s 调整 of %2\$.2f 已经被添加到你公会的数据库。";
$lang['admin_add_admin_success']             = "一封包含管理信息的 e-mail 已经被发送到 %1\$s 。";
$lang['admin_add_event_success']             = "一个百分比值 %1\$s for 一个 raid 在 %2\$s 已经被添加到你公会的数据库。";
$lang['admin_add_iadj_success']              = "一个个人 %1\$s 调整 of %2\$.2f for %3\$s 已经被添加到你公会的数据库。";
$lang['admin_add_item_success']              = "一个物品购买条目 for %1\$s,购买者 %2\$s for %3\$.2f 已经被添加到你公会的数据库。";
$lang['admin_add_member_success']            = "%1\$s 已经作为一个成员被添加到公会数据库。";
$lang['admin_add_news_success']              = '新闻条目已经被添加到你公会的数据库。';
$lang['admin_add_raid_success']              = "%1\$d/%2\$d/%3\$d raid 在 %4\$s 已经被添加到你公会的数据库。";
$lang['admin_add_style_success']             = '新样式已经被成功添加。';
$lang['admin_add_turnin_success']            = "%1\$s 已经被转移从 %2\$s 到 %3\$s。";
$lang['admin_delete_adj_success']            = "%1\$s 调整 of %2\$.2f 已经从你的公会数据库中被删除。";
$lang['admin_delete_admins_success']         = "选中的管理员已经被删除。";
$lang['admin_delete_event_success']          = "百分比值 %1\$s for 一个 raid 在 %2\$s 已经从你的公会数据库中被删除。";
$lang['admin_delete_iadj_success']           = "个人 %1\$s 调整 of %2\$.2f for %3\$s 已经从你的公会数据库中被删除。";
$lang['admin_delete_item_success']           = "物品购买条目 for %1\$s, 购买者 %2\$s for %3\$.2f 已经从你的公会数据库中被删除。";
$lang['admin_delete_members_success']        = "%1\$s, 包括全部他/她的历史, 已经从你的公会数据库中被删除。";
$lang['admin_delete_news_success']           = '新闻条目已经从你的公会数据库中被删除。';
$lang['admin_delete_raid_success']           = 'raid 和任何物品分配将从你的公会数据库中被删除。';
$lang['admin_delete_style_success']          = '样式已经被成功删除。';
$lang['admin_delete_user_success']           = "用户名为 %1\$s 的账号已被删除。";
$lang['admin_set_perms_success']             = "全部管理权限已经被更新。";
$lang['admin_transfer_history_success']      = "全部 %1\$s的历史已经被转移到 %2\$s 和 %1\$s 已经从你的公会数据库中被删除。";
$lang['admin_update_account_success']        = "你的账号设置已经被更新到数据库。";
$lang['admin_update_adj_success']            = "%1\$s 调整 of %2\$.2f 已经被更新到你公会的数据库。";
$lang['admin_update_event_success']          = "百分比值 %1\$s for 一个 raid 在 %2\$s 已经被更新到你公会的数据库。";
$lang['admin_update_iadj_success']           = "个人 %1\$s 调整 of %2\$.2f for %3\$s 已经被更新到你公会的数据库。";
$lang['admin_update_item_success']           = "物品购买条目 %1\$s, 购买者 %2\$s for %3\$.2f 已经被更新到你公会的数据库。";
$lang['admin_update_member_success']         = "成员设置 for %1\$s 已经被更新。";
$lang['admin_update_news_success']           = '新闻条目已经被更新到你公会的数据库。';
$lang['admin_update_raid_success']           = "%1\$d/%2\$d/%3\$d raid 在 %4\$s 已经被更新到你公会的数据库。";
$lang['admin_update_style_success']          = '样式已经被成功更新。';

$lang['admin_raid_success_hideinactive']     = '正在更新活跃/非活跃玩家状态...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = '你确认你想删除这个整体调整?';
$lang['confirm_delete_admins']  = '你确认你想删除选中的管理员?';
$lang['confirm_delete_event']   = '你确认你想删除这个事件?';
$lang['confirm_delete_iadj']    = '你确认你想删除这个个人调整?';
$lang['confirm_delete_item']    = '你确认你想删除这个物品?';
$lang['confirm_delete_members'] = '你确认你想删除下面的成员?';
$lang['confirm_delete_news']    = '你确认你想删除这个新闻条目?';
$lang['confirm_delete_raid']    = '你确认你想删除这个 raid?';
$lang['confirm_delete_style']   = '你确认你想删除这个样式?';
$lang['confirm_delete_users']   = '你确认你想删除下面的用户账号?';

// Log Actions
$lang['action_event_added']      = '事件已添加';
$lang['action_event_deleted']    = '事件已删除';
$lang['action_event_updated']    = '事件已更新';
$lang['action_groupadj_added']   = '整体调整已添加';
$lang['action_groupadj_deleted'] = '整体调整已删除';
$lang['action_groupadj_updated'] = '整体调整已更新';
$lang['action_history_transfer'] = '成员转移历史';
$lang['action_indivadj_added']   = '个人调整已添加';
$lang['action_indivadj_deleted'] = '个人调整已删除';
$lang['action_indivadj_updated'] = '个人调整已更新';
$lang['action_item_added']       = '物品已添加';
$lang['action_item_deleted']     = '物品已删除';
$lang['action_item_updated']     = '物品已更新';
$lang['action_member_added']     = '成员已添加';
$lang['action_member_deleted']   = '成员已删除';
$lang['action_member_updated']   = '成员已更新';
$lang['action_news_added']       = '新闻条目已添加';
$lang['action_news_deleted']     = '新闻条目已删除';
$lang['action_news_updated']     = '新闻条目已更新';
$lang['action_raid_added']       = 'Raid已添加';
$lang['action_raid_deleted']     = 'Raid已删除';
$lang['action_raid_updated']     = 'Raid已更新';
$lang['action_turnin_added']     = '上交已添加';

// Before/After
$lang['adjustment_after']  = '调整晚于';
$lang['adjustment_before'] = '调整早于';
$lang['attendees_after']   = '出席晚于';
$lang['attendees_before']  = '出席早于';
$lang['buyers_after']      = '购买者晚于';
$lang['buyers_before']     = '购买者早于';
$lang['class_after']       = '职业晚于';
$lang['class_before']      = '职业早于';
$lang['earned_after']      = '获得晚于';
$lang['earned_before']     = '获得早于';
$lang['event_after']       = '事件晚于';
$lang['event_before']      = '事件早于';
$lang['headline_after']    = '头条晚于';
$lang['headline_before']   = '头条早于';
$lang['level_after']       = '等级晚于';
$lang['level_before']      = '等级早于';
$lang['members_after']     = '成员晚于';
$lang['members_before']    = '成员早于';
$lang['message_after']     = '消息晚于';
$lang['message_before']    = '消息早于';
$lang['name_after']        = '名称晚于';
$lang['name_before']       = '名称早于';
$lang['note_after']        = '注释晚于';
$lang['note_before']       = '注释早于';
$lang['race_after']        = '种族晚于';
$lang['race_before']       = '种族早于';
$lang['raid_id_after']     = 'Raid ID 晚于';
$lang['raid_id_before']    = 'Raid ID 早于';
$lang['reason_after']      = '原因晚于';
$lang['reason_before']     = '原因早于';
$lang['spent_after']       = '花费晚于';
$lang['spent_before']      = '花费早于';
$lang['value_after']       = '价值晚于';
$lang['value_before']      = '价值早于';

// Configuration
$lang['general_settings'] = '一般设置';
$lang['guildtag'] = '公会tag / 联盟名称';
$lang['guildtag_note'] = '几乎每页的标题都会用到';
$lang['parsetags'] = '解析公会 tags';
$lang['parsetags_note'] = '下面所列的选项将在处理 raid 日志时可用。';
$lang['domain_name'] = '域名';
$lang['server_port'] = '服务器端口';
$lang['server_port_note'] = '你的 Web 服务器端口。一般是 80';
$lang['script_path'] = '脚本路径';
$lang['script_path_note'] = 'EQdkp 所在的路径，相对于域名';
$lang['site_name'] = '站点名称';
$lang['site_description'] = '站点描述';
$lang['point_name'] = '点数名称';
$lang['point_name_note'] = '如: DKP, RP, etc.';
$lang['enable_account_activation'] = '启用账户活跃';
$lang['none'] = '无';
$lang['admin'] = '管理';
$lang['default_language'] = '默认语言';
$lang['default_locale'] = '默认区域 (仅仅是字符集; 不影响语言)';
$lang['default_game'] = '默认游戏';
$lang['default_game_warn'] = '改变默认游戏将影响这个会话中的其它改变。';
$lang['default_style'] = '默认样式';
$lang['default_page'] = '默认首页';
$lang['hide_inactive'] = '隐藏不活跃成员';
$lang['hide_inactive_note'] = '隐藏在 [不活跃周期] 天内没有参加活动的成员?';
$lang['inactive_period'] = '不活跃周期';
$lang['inactive_period_note'] = '不参加 raid 但仍认为是活跃成员的宽限期';
$lang['inactive_point_adj'] = '不活跃点数调整';
$lang['inactive_point_adj_note'] = '当一个成员成为不活跃时的点数调整。';
$lang['active_point_adj'] = '活跃点数调整';
$lang['active_point_adj_note'] = '当一个成员成为活跃时的点数调整。';
$lang['enable_gzip'] = '启用 Gzip 压缩';
$lang['show_item_stats'] = '显示物品状态';
$lang['show_item_stats_note'] = '尝试从 Internet 抓取物品属性。也许会延长页面显示时间';
$lang['default_permissions'] = '默认权限';
$lang['default_permissions_note'] = '这些是未登录用户和给予新注册用户的权限。条目在 <b>加粗</b> 的是管理员权限，
                                     强烈建议不要把它们设置成默认权限。条目在 <i>斜体</i> 是插件使用的。你可以稍候去 用户管理 改变单个用户的权限。';
$lang['plugins'] = '插件';
$lang['no_plugins'] = '插件目录 (./plugins/) 是空的。';
$lang['cookie_settings'] = 'Cookie 设置';
$lang['cookie_domain'] = 'Cookie 域';
$lang['cookie_name'] = 'Cookie 名称';
$lang['cookie_path'] = 'Cookie 路径';
$lang['session_length'] = 'Session 长度 (秒)';
$lang['email_settings'] = 'E-Mail 设置';
$lang['admin_email'] = '管理员 E-Mail 地址';
$lang['backup_options'] = '备份选项';

// Admin Index
$lang['anonymous'] = '匿名';
$lang['database_size'] = '数据库大小';
$lang['eqdkp_started'] = 'EQdkp 已开始';
$lang['ip_address'] = 'IP 地址';
$lang['items_per_day'] = '物品 每天';
$lang['last_update'] = '最后更新';
$lang['location'] = '区域';
$lang['new_version_notice'] = "EQdkp 版本 %1\$s 的下载是 <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">可用的</a>.";
$lang['number_of_items'] = '物品数量';
$lang['number_of_logs'] = '日志条目数量';
$lang['number_of_members'] = '成员数量 (活跃 / 不活跃)';
$lang['number_of_raids'] = 'Raids 数量';
$lang['raids_per_day'] = 'Raids 每天';
$lang['statistics'] = '统计';
$lang['totals'] = '总共';
$lang['version_update'] = '版本更新';
$lang['who_online'] = '谁在线上';

// Style Management
$lang['style_settings'] = '样式设置';
$lang['style_name'] = '样式名称';
$lang['template'] = '模板';
$lang['element'] = '元素';
$lang['background_color'] = '背景色';
$lang['fontface1'] = '字体 1';
$lang['fontface1_note'] = '默认字体';
$lang['fontface2'] = '字体 2';
$lang['fontface2_note'] = '输入框字体';
$lang['fontface3'] = '字体 3';
$lang['fontface3_note'] = '当前未使用';
$lang['fontsize1'] = '字体大小 1';
$lang['fontsize1_note'] = '小';
$lang['fontsize2'] = '字体大小 2';
$lang['fontsize2_note'] = '中';
$lang['fontsize3'] = '字体大小 3';
$lang['fontsize3_note'] = '大';
$lang['fontcolor1'] = '字体颜色 1';
$lang['fontcolor1_note'] = '默认颜色';
$lang['fontcolor2'] = '字体颜色 2';
$lang['fontcolor2_note'] = '表格外使用的颜色 (菜单, 标题, copyright)';
$lang['fontcolor3'] = '字体颜色 3';
$lang['fontcolor3_note'] = '输入框字体颜色';
$lang['fontcolor_neg'] = '反面字体颜色';
$lang['fontcolor_neg_note'] = '负面/非法成员的颜色';
$lang['fontcolor_pos'] = '正面字体颜色';
$lang['fontcolor_pos_note'] = '正面/合法成员的颜色';
$lang['body_link'] = '链接颜色';
$lang['body_link_style'] = '链接样式';
$lang['body_hlink'] = '悬停链接颜色';
$lang['body_hlink_style'] = '悬停链接样式';
$lang['header_link'] = '头部链接';
$lang['header_link_style'] = '头部链接样式';
$lang['header_hlink'] = '悬停头部链接';
$lang['header_hlink_style'] = '悬停头部链接样式';
$lang['tr_color1'] = '表行颜色 1';
$lang['tr_color2'] = '表行颜色 2';
$lang['th_color1'] = '表头颜色';
$lang['table_border_width'] = '表格线宽度';
$lang['table_border_color'] = '表格线颜色';
$lang['table_border_style'] = '表格线样式';
$lang['input_color'] = '输入框背景色';
$lang['input_border_width'] = '输入框边框宽度';
$lang['input_border_color'] = '输入框边框颜色';
$lang['input_border_style'] = '输入框边框样式';
$lang['style_configuration'] = '样式选项';
$lang['style_date_note'] = '为日期/时间字段，用法请参见PHP <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> 函数。';
$lang['attendees_columns'] = '出席者列';
$lang['attendees_columns_note'] = '查看一个 raid 时出席者的列数';
$lang['date_notime_long'] = '含时间的日期 (长)';
$lang['date_notime_short'] = '不含时间的日期 (短)';
$lang['date_time'] = '日期和时间';
$lang['logo_path'] = 'Logo 文件名';

// Errors
$lang['error_invalid_adjustment'] = '一个有效的调整没有被提供。';
$lang['error_invalid_plugin']     = '一个有效的插件没有被提供。';
$lang['error_invalid_style']      = '一个有效的样式没有被提供。';

// Verbose log entry lines
$lang['new_actions']           = '最新管理操作';
$lang['vlog_event_added']      = "%1\$s 添加了事件 '%2\$s' 值 %3\$.2f 点。";
$lang['vlog_event_updated']    = "%1\$s 更新了事件 '%2\$s'。";
$lang['vlog_event_deleted']    = "%1\$s 删除了事件 '%2\$s'。";
$lang['vlog_groupadj_added']   = "%1\$s 添加了一个总体调整 of %2\$.2f 点。";
$lang['vlog_groupadj_updated'] = "%1\$s 更新了一个总体调整 of %2\$.2f 点。";
$lang['vlog_groupadj_deleted'] = "%1\$s 删除了一个总体调整 of %2\$.2f 点。";
$lang['vlog_history_transfer'] = "%1\$s 转移了 %2\$s的历史到 %3\$s。";
$lang['vlog_indivadj_added']   = "%1\$s 添加了一个个人调整 of %2\$.2f to %3\$d 成员。";
$lang['vlog_indivadj_updated'] = "%1\$s 更新了一个个人调整 of %2\$.2f to %3\$s。";
$lang['vlog_indivadj_deleted'] = "%1\$s 删除了一个个人调整 of %2\$.2f to %3\$s。";
$lang['vlog_item_added']       = "%1\$s 添加了物品 '%2\$s' 到 %3\$d 成员 for %4\$.2f 点。";
$lang['vlog_item_updated']     = "%1\$s 更新了物品 '%2\$s' 到 %3\$d 成员。";
$lang['vlog_item_deleted']     = "%1\$s 删除了物品 '%2\$s' 到 %3\$d 成员。";
$lang['vlog_member_added']     = "%1\$s 添加了成员 %2\$s。";
$lang['vlog_member_updated']   = "%1\$s 更新了成员 %2\$s。";
$lang['vlog_member_deleted']   = "%1\$s 删除了成员 %2\$s。";
$lang['vlog_news_added']       = "%1\$s 添加了新闻条目 '%2\$s'。";
$lang['vlog_news_updated']     = "%1\$s 更新了新闻条目 '%2\$s'。";
$lang['vlog_news_deleted']     = "%1\$s 删除了新闻条目 '%2\$s'。";
$lang['vlog_raid_added']       = "%1\$s 添加了一个 raid 在 '%2\$s'。";
$lang['vlog_raid_updated']     = "%1\$s 更新了一个 raid 在 '%2\$s'。";
$lang['vlog_raid_deleted']     = "%1\$s 删除了一个 raid 在 '%2\$s'。";
$lang['vlog_turnin_added']     = "%1\$s 添加了一个上交来自 %2\$s to %3\$s for '%4\$s'。";

// Location messages
$lang['adding_groupadj'] = '正在添加一个总体调整';
$lang['adding_indivadj'] = '正在添加一个个人调整';
$lang['adding_item'] = '正在添加一个物品';
$lang['adding_news'] = '正在添加一个新闻条目';
$lang['adding_raid'] = '正在添加一个 Raid';
$lang['adding_turnin'] = '正在添加一个上交';
$lang['editing_groupadj'] = '正在编辑总体调整';
$lang['editing_indivadj'] = '正在编辑个人调整';
$lang['editing_item'] = '正在编辑物品';
$lang['editing_news'] = '正在编辑新闻条目';
$lang['editing_raid'] = '正在编辑 Raid';
$lang['listing_events'] = '正在列表事件';
$lang['listing_groupadj'] = '正在列表总体调整';
$lang['listing_indivadj'] = '正在列表个人调整';
$lang['listing_itemhist'] = '正在列表物品历史';
$lang['listing_itemvals'] = '正在列表物品价值';
$lang['listing_members'] = '正在列表成员';
$lang['listing_raids'] = '正在列表 Raids';
$lang['managing_config'] = '正在管理 EQdkp 选项';
$lang['managing_members'] = '正在管理公会成员';
$lang['managing_plugins'] = '正在管理插件';
$lang['managing_styles'] = '正在管理样式';
$lang['managing_users'] = '正在管理用户账号';
$lang['parsing_log'] = '正在处理一个日志';
$lang['viewing_admin_index'] = '正在查看管理首页';
$lang['viewing_event'] = '正在查看事件';
$lang['viewing_item'] = '正在查看物品';
$lang['viewing_logs'] = '正在查看日志';
$lang['viewing_member'] = '正在查看成员';
$lang['viewing_mysql_info'] = '正在查看 MySQL 信息';
$lang['viewing_news'] = '正在查看新闻';
$lang['viewing_raid'] = '正在查看 Raid';
$lang['viewing_stats'] = '正在查看状态';
$lang['viewing_summary'] = '正在查看概览';

// Help lines
$lang['b_help'] = '粗体文本: [b]文本[/b] (shift+alt+b)';
$lang['i_help'] = '斜体文本: [i]文本[/i] (shift+alt+i)';
$lang['u_help'] = '下划线文本: [u]文本[/u] (shift+alt+u)';
$lang['q_help'] = '引用文本: [quote]文本[/quote] (shift+alt+q)';
$lang['c_help'] = '居中文本: [center]文本[/center] (shift+alt+c)';
$lang['p_help'] = '插入图片 [img]http://image_url[/img] (shift+alt+p)';
$lang['w_help'] = '插入 URL: [url]http://URL[/url] 或 [url=http://url]text[/url] (shift+alt+w)';
$lang['it_help'] = '插入物品: e.g. [item]Judgement Breastplate[/item] (shift+alt+t)';
$lang['ii_help'] = '插入物品图标: e.g. [itemicon]Judgement Breastplate[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = '添加新成员';
$lang['list_edit_del_member'] = '列表，编辑或删除成员';
$lang['edit_ranks'] = '编辑成员头衔';
$lang['transfer_history'] = '转移成员历史';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'MySQL 信息';
$lang['eqdkp_tables'] = 'EQdkp 表';
$lang['table_name'] = '表名';
$lang['rows'] = '行';
$lang['table_size'] = '表大小';
$lang['index_size'] = '索引大小';
$lang['num_tables'] = "%d 表";

//Backup
$lang['backup'] = '备份';
$lang['backup_title'] = '创建一个数据库备份';
$lang['create_table'] = '添加 \'CREATE TABLE\' 语句?';
$lang['skip_nonessential'] = '跳过非必须数据?<br />将不产生插入表 eqdkp_sessions。';
$lang['gzip_content'] = 'GZIP 允许?<br />如果 GZIP 是启用的，将产生一个更小的文件。';
$lang['backup_database'] = '备份数据库';

// plus
$lang['in_database']  = '已保存在数据库';

//Log Users Actions
$lang['action_user_added']     = '用户已添加';
$lang['action_user_deleted']   = '用户已删除';
$lang['action_user_updated']   = '用户已更新';
$lang['vlog_user_added']     = "%1\$s 已添加用户 %2\$s。";
$lang['vlog_user_updated']   = "%1\$s 已更新用户 %2\$s。";
$lang['vlog_user_deleted']   = "%1\$s 已删除用户 %2\$s。";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP Pool 已添加";
$lang['action_multidkp_deleted']   = "MultiDKP Pool 已删除";
$lang['action_multidkp_updated']   = "MultiDKP Pool 已更新";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s 添加了 MultiDKP Pool %2\$s zu。";
$lang['vlog_multidkp_updated']   = "%1\$s 更新了 MultiDKP Pool %2\$s。";
$lang['vlog_multidkp_deleted']   = "%1\$s 删除了 MultiDKP Pool %2\$s。";

$lang['default_style_overwrite']   = "覆盖用户设置 (每个用户使用默认样式)";

#Plugins
$lang['description'] = 'Description';
$lang['manual'] = 'Manual';
$lang['homepage'] = 'Homepage';
$lang['readme'] = 'Read me';
$lang['link'] = 'Link';
$lang['infos'] = 'Infos';
$lang['plugin_inst_sql_note'] = 'An SQL error during install does not necessary implies a broken plugin installation. Try using the plugin, if errors occur please de- and reinstall the plugin.';

?>
