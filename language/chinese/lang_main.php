<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 * begin: Wed December 18 2002
 *
 * $Id$
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

$lang['XML_LANG'] = 'cn';
$lang['ISO_LANG_SHORT'] = 'cn_CN';
$lang['ISO_LANG_NAME'] = '闻条目 (CN)';

// Linknames
$lang['rp_link_name']   = "Raid 计划";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s 管理";
$lang['listadj_title']        = '整体调整列表';
$lang['listevents_title']     = '查看事件';
$lang['listiadj_title']       = '单独调整列表';
$lang['listitems_title']      = '物品价值';
$lang['listnews_title']       = '新闻条目';
$lang['listmembers_title']    = '标准成员';
$lang['listpurchased_title']  = '物品历史';
$lang['listraids_title']      = 'Raids 列表';
$lang['login_title']          = '登录';
$lang['message_title']        = 'EQdkp: 信息';
$lang['register_title']       = '注册';
$lang['settings_title']       = '账号设置';
$lang['stats_title']          = "%1\$s 状态";
$lang['summary_title']        = '新闻摘要';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "查看 %1\$s 的 Raid 记录";
$lang['viewitem_title']       = "查看 %1\$s 的交易记录";
$lang['viewmember_title']     = "%1\$s 的历史记录";
$lang['viewraid_title']       = 'Raid 摘要';

// Main Menu
$lang['menu_admin_panel'] = '管理面板';
$lang['menu_events'] = '事件';
$lang['menu_itemhist'] = '物品历史';
$lang['menu_itemval'] = '物品价值';
$lang['menu_news'] = '新闻';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = '注册';
$lang['menu_settings'] = '设置';
$lang['menu_members'] = 'Characters';
$lang['menu_standings'] = '成员信息';
$lang['menu_stats'] = '状态';
$lang['menu_summary'] = '摘要';

// Column Headers
$lang['account'] = '账号';
$lang['action'] = '动作';
$lang['active'] = '行为';
$lang['add'] = '添加';
$lang['added_by'] = '添加者';
$lang['adjustment'] = '调整';
$lang['administration'] = '管理';
$lang['administrative_options'] = '管理选项';
$lang['admin_index'] = '管理首页';
$lang['attendance_by_event'] = '按活动出席';
$lang['attended'] = '出席';
$lang['attendees'] = '出席者';
$lang['average'] = '平均';
$lang['buyer'] = '购买者';
$lang['buyers'] = '购买者';
$lang['class'] = '职业';
$lang['armor'] = '护甲';
$lang['type'] = '护甲';
$lang['class_distribution'] = '职业分布';
$lang['class_summary'] = "职业摘要: %1\$s to %2\$s";
$lang['configuration'] = '设置';
$lang['config_plus']	= 'PLUS 设置';
$lang['plus_vcheck']	= '更新检查';
$lang['current'] = '当前';
$lang['date'] = '日期';
$lang['delete'] = '删除';
$lang['delete_confirmation'] = '删除确认';
$lang['dkp_value'] = "%1\$s 值";
$lang['drops'] = '掉落';
$lang['earned'] = '获得';
$lang['enter_dates'] = '进入日期';
$lang['eqdkp_index'] = 'EQdkp 首页';
$lang['eqdkp_upgrade'] = 'EQdkp 更新';
$lang['event'] = '事件';
$lang['events'] = '事件';
$lang['filter'] = '过滤器';
$lang['first'] = '第一次';
$lang['rank'] = '头衔';
$lang['general_admin'] = '普通管理';
$lang['get_new_password'] = '得到一个新的密码';
$lang['group_adj'] = '整体调整';
$lang['group_adjustments'] = '整体调整';
$lang['individual_adjustments'] = '个人调整';
$lang['individual_adjustment_history'] = '个人调整历史';
$lang['indiv_adj'] = '个人调整';
$lang['ip_address'] = 'IP 地址';
$lang['item'] = '物品';
$lang['items'] = '物品';
$lang['item_purchase_history'] = '物品交易历史';
$lang['last'] = '最后';
$lang['lastloot'] = '最后捡取';
$lang['lastraid'] = '最后 Raid';
$lang['last_visit'] = '最后 Visit';
$lang['level'] = '等级';
$lang['log_date_time'] = '这个日志的 日期/时间';
$lang['loot_factor'] = '捡取者';
$lang['loots'] = '捡取';
$lang['manage'] = '处理';
$lang['member'] = '成员';
$lang['members'] = '成员';
$lang['members_present_at'] = "当前成员在 %1\$s 在 %2\$s";
$lang['miscellaneous'] = '杂项';
$lang['name'] = '名字';
$lang['news'] = '新闻';
$lang['note'] = '注释';
$lang['online'] = '在线';
$lang['options'] = '设置';
$lang['paste_log'] = '在下面粘贴日志';
$lang['percent'] = '当前';
$lang['permissions'] = '许可';
$lang['per_day'] = '每天';
$lang['per_raid'] = '每 Raid';
$lang['pct_earned_lost_to'] = '% 获得点数到';
$lang['preferences'] = '参数';
$lang['purchase_history_for'] = "%1\$s 的交易历史";
$lang['quote'] = '引用';
$lang['race'] = '种族';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'Raid 出席历史';
$lang['raids_lifetime'] = "有效时间 (%1\$s - %2\$s)";
$lang['raids_x_days'] = "持续 %1\$d 天";
$lang['rank_distribution'] = '头衔分布';
$lang['recorded_raid_history'] = "已记录的 Raid 历史 for %1\$s";
$lang['reason'] = '原因';
$lang['registration_information'] = '注册信息';
$lang['result'] = '结果';
$lang['session_id'] = 'Session ID';
$lang['settings'] = '设置';
$lang['spent'] = '花费';
$lang['summary_dates'] = "Raid 摘要: %1\$s 到 %2\$s";
$lang['themes'] = '主题';
$lang['time'] = '时间';
$lang['total'] = '总共';
$lang['total_earned'] = '总共获得';
$lang['total_items'] = '总计物品';
$lang['total_raids'] = '总共 Raids';
$lang['total_spent'] = '总共花费';
$lang['transfer_member_history'] = '成员转移历史';
$lang['turn_ins'] = '物品交易';
$lang['type'] = '类型';
$lang['update'] = '更新';
$lang['updated_by'] = '更新者';
$lang['user'] = '用户';
$lang['username'] = '用户名';
$lang['value'] = '值';
$lang['view'] = '查看';
$lang['view_action'] = '查看行为';
$lang['view_logs'] = '查看日志';

// Page Foot Counts
$lang['listadj_footcount']               = "... 创建 %1\$d 调整 / %2\$d 每页";
$lang['listevents_footcount']            = "... 创建 %1\$d 事件 / %2\$d 每页";
$lang['listiadj_footcount']              = "... 创建 %1\$d 个人调整 / %2\$d 每页";
$lang['listitems_footcount']             = "... 创建 %1\$d 唯一物品 / %2\$d 每页";
$lang['listmembers_active_footcount']    = "... 创建 %1\$d 活跃成员 / %2\$s全部显示</a>";
$lang['listmembers_compare_footcount']   = "... 比较 %1\$d 成员";
$lang['listmembers_footcount']           = "... 创建 %1\$d 成员";
$lang['listnews_footcount']              = "... 创建 %1\$d 新条目 / %2\$d 每页";
$lang['listpurchased_footcount']         = "... 创建 %1\$d 物品 / %2\$d 每页";
$lang['listraids_footcount']             = "... 创建 %1\$d raid / %2\$d 每页";
$lang['stats_active_footcount']          = "... 创建 %1\$d 活跃成员 / %2\$s全部显示</a>";
$lang['stats_footcount']                 = "... 创建 %1\$d 成员";
$lang['viewevent_footcount']             = "... 创建 %1\$d raid";
$lang['viewitem_footcount']              = "... 创建 %1\$d 物品";
$lang['viewmember_adjustment_footcount'] = "... 创建 %1\$d 个人调整";
$lang['viewmember_item_footcount']       = "... 创建 %1\$d 购买物品 / %2\$d 每页";
$lang['viewmember_raid_footcount']       = "... 创建 %1\$d 出席 raid / %2\$d 每页";
$lang['viewraid_attendees_footcount']    = "... 创建 %1\$d 出席者";
$lang['viewraid_drops_footcount']        = "... 创建 %1\$d 掉落";

// Submit Buttons
$lang['close_window'] = '关闭窗口';
$lang['compare_members'] = '比较成员';
$lang['create_news_summary'] = '新建新闻摘要';
$lang['login'] = '登录';
$lang['logout'] = '登出';
$lang['log_add_data'] = '添加数据到表单';
$lang['lost_password'] = '丢失密码';
$lang['no'] = '不';
$lang['proceed'] = '继续';
$lang['reset'] = '重置';
$lang['set_admin_perms'] = '设置管理员许可';
$lang['submit'] = '提交';
$lang['upgrade'] = '更新';
$lang['yes'] = '是';

// Form Element Descriptions
$lang['admin_login'] = '管理员登录';
$lang['confirm_password'] = '确认密码';
$lang['confirm_password_note'] = '如果上面已经更改，只需要确认新的密码';
$lang['current_password'] = '当前密码';
$lang['current_password_note'] = '如果想更改用户名/密码，必须确认当前密码';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email 地址';
$lang['ending_date'] = '结束日期';
$lang['from'] = '从';
$lang['guild_tag'] = '公会 Tag';
$lang['language'] = '语言';
$lang['new_password'] = '新密码';
$lang['new_password_note'] = '在更改密码的时候，只需要提供一个新的密码';
$lang['password'] = '密码';
$lang['remember_password'] = '记住我 (cookie)';
$lang['starting_date'] = '开始日期';
$lang['style'] = '样式';
$lang['to'] = '到';
$lang['username'] = '用户名';
$lang['users'] = '用户';

// Pagination
$lang['next_page'] = '下一页';
$lang['page'] = '页';
$lang['previous_page'] = '上一页';

// Permission Messages
$lang['noauth_default_title'] = '许可拒绝';
$lang['noauth_u_event_list'] = '你没有被许可 列表事件 。';
$lang['noauth_u_event_view'] = '你没有被许可 查看事件 。';
$lang['noauth_u_item_list'] = '你没有被许可 列表物品 。';
$lang['noauth_u_item_view'] = '你没有被许可 查看物品 。';
$lang['noauth_u_member_list'] = '你没有被许可 查看成员状态 。';
$lang['noauth_u_member_view'] = '你没有被许可 查看成员历史 。';
$lang['noauth_u_raid_list'] = '你没有被许可 列表 raids 。';
$lang['noauth_u_raid_view'] = '你没有被许可 查看 raids 。';

// Submission Success Messages
$lang['add_itemvote_success'] = '你对这件物品的投票已经被记录。';
$lang['update_itemvote_success'] = '你对这件物品的投票已经被更新。';
$lang['update_settings_success'] = '你的用户设置已经被更新。';

// Form Validation Errors
$lang['fv_alpha_attendees'] = '人物\' 名称在 EverQuest 只能包含字母。';
$lang['fv_already_registered_email'] = 'e-mail 地址已经被注册。';
$lang['fv_already_registered_username'] = '用户名已经被注册。';
$lang['fv_difference_transfer'] = '一个历史转移必须在两个不同的人之间进行。';
$lang['fv_difference_turnin'] = '一个上交必须在两个不同的人之间进行。';
$lang['fv_invalid_email'] = 'e-mail 地址看起来不是有效的。';
$lang['fv_match_password'] = '密码字段必须匹配。';
$lang['fv_member_associated']  = "%1\$s 已经分配到另一个用户。";
$lang['fv_number'] = '必须是一个数字。';
$lang['fv_number_adjustment'] = '调整值字段必须是数字。';
$lang['fv_number_alimit'] = '调整限制字段必须是数字。';
$lang['fv_number_ilimit'] = '物品限制字段必须是数字。';
$lang['fv_number_inactivepd'] = '非活跃期必须是数字。';
$lang['fv_number_pilimit'] = '购买物品限制 必须是数字。';
$lang['fv_number_rlimit'] = 'raids 限制必须是数字。';
$lang['fv_number_value'] = '值字段必须是数字。';
$lang['fv_number_vote'] = '投票字段必须是数字。';
$lang['fv_date'] = '请从日历中选择一个有效的日期。';
$lang['fv_range_day'] = '日 字段必须是介于 1 和 31 之间的整数。';
$lang['fv_range_hour'] = '小时 字段必须是介于 0 和 23 之间的整数。';
$lang['fv_range_minute'] = '分 字段必须是介于 0 和 59 之间的整数。';
$lang['fv_range_month'] = '月 字段必须是介于 1 和 12 之间的整数。';
$lang['fv_range_second'] = '秒 字段必须是介于 0 和 59 之间的整数。';
$lang['fv_range_year'] = '年 字段必须是一个最小值为 1998 的整数。';
$lang['fv_required'] = '必填字段';
$lang['fv_required_acro'] = '公会缩写字段是必须的。';
$lang['fv_required_adjustment'] = '调整值字段是必须的。';
$lang['fv_required_attendees'] = '这个 raid 至少要有一位出席者。';
$lang['fv_required_buyer'] = '必须选择一个购买者。';
$lang['fv_required_buyers'] = '必须选择至少一个购买者。';
$lang['fv_required_email'] = 'e-mail 地址字段是必须的。';
$lang['fv_required_event_name'] = '必须选择一个事件。';
$lang['fv_required_guildtag'] = '公会tag 字段是必须的。';
$lang['fv_required_headline'] = '标题字段是必须的。';
$lang['fv_required_inactivepd'] = '如果 隐藏非活跃成员 选项为 是，非活跃期限值必须被设置。';
$lang['fv_required_item_name'] = '物品名称字段必须被填写，或一个物品必须被选择。';
$lang['fv_required_member'] = '必须选择一个成员。';
$lang['fv_required_members'] = '必须选择至少一个成员。';
$lang['fv_required_message'] = '消息字段是必须的。';
$lang['fv_required_name'] = '名称字段是必须的。';
$lang['fv_required_password'] = '密码字段是必须的。';
$lang['fv_required_raidid'] = '一个 raid 必须被选择。';
$lang['fv_required_user'] = '用户名字段是必须的。';
$lang['fv_required_value'] = '值字段是必须的。';
$lang['fv_required_vote'] = '投票字段是必须的。';

// Miscellaneous
$lang['added'] = '已添加';
$lang['additem_raidid_note'] = "只有两周内的 raids 会被显示 / %1\$s全部显示</a>";
$lang['additem_raidid_showall_note'] = '显示全部 raids';
$lang['addraid_datetime_note'] = '如果你粘贴一个日志，日期和时间将被自动找到。';
$lang['addraid_value_note'] = '一次性奖励；如果留空，将使用事件的预定值。';
$lang['add_items_from_raid'] = '从这次 Raid 添加物品';
$lang['deleted'] = '已删除';
$lang['done'] = '完成';
$lang['enter_new'] = '输入新的';
$lang['error'] = '错误';
$lang['head_admin'] = '领队管理员';
$lang['hold_ctrl_note'] = '按 CTRL 选择多个';
$lang['list'] = '列表';
$lang['list_groupadj'] = '列表整体调整';
$lang['list_events'] = '列表事件';
$lang['list_indivadj'] = '列表个人调整';
$lang['list_items'] = '列表物品';
$lang['list_members'] = '列表成员';
$lang['list_news'] = '列表新闻';
$lang['list_raids'] = '列表 Raids';
$lang['may_be_negative_note'] = '可能被否定';
$lang['not_available'] = '不可用';
$lang['no_news'] = '无新闻条目被找到。';
$lang['of_raids'] = "%1\$d%% of raids";
$lang['or'] = '或';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = '预览';
$lang['required_field_note'] = '标记为 * 是必填字段。';
$lang['select_1ofx_members'] = "选择 1 of %1\$d 成员...";
$lang['select_existing'] = '选择存在';
$lang['select_version'] = '选择你更新自的 EQdkp 版本';
$lang['success'] = '成功';
$lang['s_admin_note'] = '这些字段不能够被用户修改。';
$lang['transfer_member_history_description'] = '这将转移成员的全部历史 （raids，物品， 调整） 到另一个成员。';
$lang['updated'] = '已更新';
$lang['upgrade_complete'] = '你的 EQdkp 安装已成功更新。<br /><br /><b class="negative">为了安全，移除这个文件。!</b>';

// Settings
$lang['account_settings'] = '账号设置';
$lang['adjustments_per_page'] = '显示的调整';
$lang['basic'] = '基本';
$lang['events_per_page'] = '每页显示的事件';
$lang['items_per_page'] = '每页显示的物品';
$lang['news_per_page'] = '每页显示的新闻';
$lang['raids_per_page'] = '每页显示的 Raids';
$lang['associated_members'] = '分配成员';
$lang['guild_members'] = '公会成员';
$lang['default_locale'] = '默认区域';


// Error messages
$lang['error_account_inactive'] = '你的账号已冻结。';
$lang['error_already_activated'] = '那个账号已经被激活。';
$lang['error_invalid_email'] = '一个有效的 e-mail 地址没有被提供。';
$lang['error_invalid_event_provided'] = '一个有效的事件 id 没有被提供。';
$lang['error_invalid_item_provided'] = '一个有效的物品 id 没有被提供。';
$lang['error_invalid_key'] = '你已经提供了一个无效的激活关键字。';
$lang['error_invalid_name_provided'] = '一个有效的成员名称没有被提供。';
$lang['error_invalid_news_provided'] = '一个有效的新闻 id 没有被提供。';
$lang['error_invalid_raid_provided'] = '一个有效的 raid id 没有被提供。';
$lang['error_user_not_found'] = '一个有效的用户名没有被提供。';
$lang['incorrect_password'] = '不正确的密码';
$lang['invalid_login'] = '你已经提供了一个不正确的密码或无效的用户名。';
$lang['not_admin'] = '你不是一个管理员。';

// Registration
$lang['account_activated_admin']   = '账号已经激活。一封 e-mail 已经被发送到用户提醒这次改变。';
$lang['account_activated_user']    = "账号已经激活并且你可以 %1\$s登录%2\$s。";
$lang['password_sent'] = '你的账号的新密码已经通过 e-mail 发送给你。';
$lang['register_activation_self']  = "你的账号已经被创建，但是在你使用前你需要激活它。<br /><br />一封 e-mail 已经被发送到 %1\$s 包含如何激活账号的信息。";
$lang['register_activation_admin'] = "你的账号已经被创建，但是在你使用前管理员需要激活它。<br /><br />一封 e-mail 已经被发送到 %1\$s 包含更多信息。";
$lang['register_activation_none']  = "你的账号已经被创建并且你现在可以 %1\$s登录%2\$s。<br /><br />一封 e-mail 已经被发送到 %3\$s 包含更多信息。";

//plus
$lang['news_submitter'] = '提交者';
$lang['news_submitat'] = 'at';
$lang['droprate_loottable'] = "捡取表 for";
$lang['droprate_name'] = "物品名称";
$lang['droprate_count'] = "计数";
$lang['droprate_drop'] = "掉落 %";

$lang['Itemsearch_link'] = "物品搜索";
$lang['Itemsearch_search'] = "物品搜索 :";
$lang['Itemsearch_searchby'] = "搜索按 :";
$lang['Itemsearch_item'] = "物品 ";
$lang['Itemsearch_buyer'] = "购买者 ";
$lang['Itemsearch_raid'] = "Raid ";
$lang['Itemsearch_unique'] = "唯一物品结果 :";
$lang['Itemsearch_no'] = "不";
$lang['Itemsearch_yes'] = "是";

$lang['bosscount_player'] = "玩家: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "物品: ";
$lang['bosscount_dkptotal'] = "总 DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - 添加 Pool";
$lang['Multi_pageheader'] 		= "MultiDKP - 显示 Pools";
$lang['Multi_events'] 				= "事件:";
$lang['Multi_eventname'] 				= "事件名称";
$lang['Multi_discnottolong'] 	= "(列名) - 这个不应当太长，否则表将变大。.选择例如 MC, BWL, AQ 等。 !";
$lang['Multi_kontoname_short']= "账号名:";
$lang['Multi_discr'] 					= "描述:";
$lang['Multi_events'] 				= "这个 Pool 的事件";


$lang['Multi_addkonto'] 			  = "添加 MultiDKP Pool";
$lang['Multi_updatekonto'] 			= "改变 Pool";
$lang['Multi_deletekonto'] 			= "删除 Pool";
$lang['Multi_viewkonten']			  = "显示 MultiDKP Pools";
$lang['Multi_chooseevents']			= "选择事件";
$lang['multi_footcount'] 				= "... %1\$d DKP Pools / %2\$d 每页";
$lang['multi_error_invalid']    = "没有已分配的 Pools ....";

$lang['Multi_required_event']   = "你必须至少选择一个事件!";
$lang['Multi_required_name']    = "你必须插入一个名称!";
$lang['Multi_required_disc']    = "你必须插入一个描述!";
$lang['Multi_admin_add_multi_success'] = "Pool %1\$s ( %2\$s ) 含有事件 %3\$s 已经被添加到数据库。";
$lang['Multi_admin_update_multi_success'] = "Pool %1\$s ( %2\$s ) 含有事件 %3\$s 已经被改变到数据库。";
$lang['Multi_admin_delete_success']           = "Pool %1\$s 已经被从数据库中删除。";
$lang['Multi_confirm_delete']    = '你确实想删除那个 Pool 吗?';


##########

$lang['Multi_total_cost']   										= '这个 Pool 的总点数';
$lang['Multi_Accs']    													= 'MultiDKP Pool';

//update

$lang['upd_eqdkp_status']    										= 'EQDKP 更新状态';
$lang['upd_system_status']    									= '系统状态';
$lang['upd_template_status']    								= '模板状态';
$lang['upd_gamefile_status']                    = 'Game Status';
$lang['upd_update_need']    										= '需要更新!';
$lang['upd_update_need_link']    								= '安装全部必须的组件';
$lang['upd_no_update']    											= '没有更新可用。系统是最新的。';
$lang['upd_status']    													= '状态';
$lang['upd_state_error']    										= '错误';
$lang['upd_sql_string']    											= 'SQL 命令';
$lang['upd_sql_status_done']    								= '完成';
$lang['upd_sql_error']    											= '错误';
$lang['upd_sql_footer']    											= 'SQL 命令已执行';
$lang['upd_sql_file_error']    									= '错误: 需要的 SQL 文件 %1\$s 无法被找到!';
$lang['upd_eqdkp_system_title']    							= 'EQDKP 系统组件更新';
$lang['upd_plus_version']    										= 'EQDKP Plus 版本';
$lang['upd_plus_feature']    										= '特性';
$lang['upd_plus_detail']    										= '详细';
$lang['upd_update']    													= '更新';
$lang['upd_eqdkp_template_title']    						= 'EQDKP 模板更新';
$lang['upd_eqdkp_gamefile_title']               = 'EQDKP game update';
$lang['upd_gamefile_availversion']              = 'Available Version';
$lang['upd_gamefile_instversion']               = 'Installed Version';
$lang['upd_template_name']    									= '模板名称';
$lang['upd_template_state']    									= '模板状态';
$lang['upd_template_filestate']    							= '模板目录可用';
$lang['upd_link_install']    										= '更新';
$lang['upd_link_reinstall']    									= '重新安装';
$lang['upd_admin_need_update']    							= '一个数据库错误已经被发现。系统不是最新的并且需要更新。';
$lang['upd_admin_link_update']									= '点击这里解决这个问题';
$lang['upd_backto']    													= '回到概览';

// Event Icon
$lang['event_icon_header']    								  = '选择事件图标';

//update Itemstats
$lang['updi_header']    								    	= '刷新 Itemstats 数据';
$lang['updi_header2']    								    	= 'Itemstats 信息';
$lang['updi_action']    								    	= '动作';
$lang['updi_notfound']    								    = '未找到';
$lang['updi_writeable_ok']    							  = '文件是可写的';
$lang['updi_writeable_no']    								= '文件是不可写的';
$lang['updi_help']    								    		= '描述';
$lang['updi_footcount']    								    = '物品刷新';
$lang['updi_curl_bad']    								    = '必须的 PHP 函数 cURL 无法被找到。也许 Itemstats 将无法完美地工作。请联系你的管理员！';
$lang['updi_curl_ok']    								    	= 'cURL 找到。';
$lang['updi_fopen_bad']    								    = '必须的 PHP 函数 fopen 无法被找到。 也许 Itemstats 将无法完美地工作。请联系你的管理员！';
$lang['updi_fopen_ok']    								    = 'fopen 找到。';
$lang['updi_nothing_found']						    		= '没有找到物品';
$lang['updi_itemscount']  						    		= '物品缓存条目:';
$lang['updi_baditemscount']						    		= '损坏的条目:';
$lang['updi_items']										    		= '物品在数据库中:';
$lang['updi_items_duplicate']					    		= '{With double items}';
$lang['updi_show_all']    								    = '使用 Itemstats 列表所有物品';
$lang['updi_refresh_all']    								  = '删除全部物品并刷新它们';
$lang['updi_refresh_bad']    								  = '仅刷新损坏的物品';
$lang['updi_refresh_raidbank']    						= '刷新 Raidbanker 物品';
$lang['updi_refresh_tradeskill']   						= '刷新 Tradeskill 物品';
$lang['updi_help_show_all']    								= 'Therby all items will be shown with their stats. Bad stats will be refreshed. (recommended)';
$lang['updi_help_refresh_all']  							= '删除当前的物品缓存并且尝试刷新所有在 EQDKP 显示的物品。警告: 如果你和一个论坛共享物品缓存，论坛将不被刷新。基于你的 Web 服务器到 Allakhazam.com 的速度和能力，这个操作将花费几分钟。可能你的 Web 服务器设置组织了连接。此时请联系你的系统管理员。';
$lang['updi_help_refresh_bad']    						= '从缓存中删除所有损坏物品并刷新它们。';
$lang['updi_help_refresh_raidbank']    				= '如果 Raidbanker 已安装, Itemstats uses the entered items of the banker.';
$lang['updi_help_refresh_Tradeskill']    			= '当 Tradeskill 已安装, the entered items will be updated by Itemstats.';

$lang['updi_active'] 					   							= '活动的';
$lang['updi_inactive']    										= '不活动的';

$lang['fontcolor']    			  = '字体颜色';
$lang['Warrior']    					= '战士';
$lang['Rogue']    						= '潜行者';
$lang['Hunter']    						= '猎人';
$lang['Paladin']    					= '圣骑士';
$lang['Priest']    						= '牧师';
$lang['Druid']    						= '德鲁伊';
$lang['Shaman']    						= '萨满祭司';
$lang['Warlock']    					= '术士';
$lang['Mage']    							= '法师';

# Reset DB Feature
$lang['reset_header']    			= '重置 EQDKP 数据';
$lang['reset_infotext']  			= '危险!!! 删除的数据将不能恢复!!! 先做一个备份。确认这个操作， 输入 DELETE 在下面的编辑框。';
$lang['reset_type']    				= '数据类型';
$lang['reset_disc']    				= '描述';
$lang['reset_sec']    				= 'Certificate';
$lang['reset_action']    			= '动作';

$lang['reset_news']					  = '新闻';
$lang['reset_news_disc']		  = '从数据库中删除全部新闻。';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = '从数据库中删除全部 raids 和物品并且重置全部 DKP 点数到零。';
$lang['reset_ALL']   					= '全部';
$lang['reset_ALL_DISC']				= '删除每个 raid ，物品和成员。完整的数据重置。 (不删除用户)。';

$lang['reset_confirm_text']	  = ' 插入这里 =>';
$lang['reset_confirm']			  = '删除';

// Armory Menu
$lang['lm_armorylink1']				= 'Armory';
$lang['lm_armorylink2']				= '天赋';
$lang['lm_armorylink3']				= '公会';

$lang['updi_update_ready']			= '物品已经被成功更新。 你可以 <a href="#" onclick="javascript:parent.closeWindow()" >关闭</a> 这个窗口。';
$lang['updi_update_alternative']= '选择更新模式以避免超时。';
$lang['zero_sum']				= ' on Zero SUM DKP';

//Hybrid
$lang['Hybrid']				= '混合';

$lang['Jump_to'] 				= '看视频在 ';
$lang['News_vid_help'] 			= 'To embed videos just post the link to the video without [tags]. supported videosites: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire. ';

$lang['SubmitNews'] 		   = '提交新闻';
$lang['SubmitNews_help'] 	   = 'You have a good News? Submit the News and share with all Eqdkp Plus Users.';

$lang['MM_User_Confirm']	   = 'Select your Admin Account? If you take of you Admin Permission, this can only be restored in the Database';

$lang['beta_warning']	   	   = 'Warning this EQDKP-Plus Beta Version must not be used on a live system! This Version stop working if a stable version is available. Check <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> for updates!';

$lang['news_comment']        = '评论';
$lang['news_comments']       = '评论';

$lang['comments_no_comments']	   = '无条目';
$lang['comments_comments_raid']	   = '评论';
$lang['comments_write_comment']	   = '写一个评论';
$lang['comments_send_comment']	   = '保存评论';
$lang['comments_save_wait']	   	   = '请稍侯，评论正在保存...';


$lang['news_nocomments'] 	 		    = '禁止评论';
$lang['news_readmore_button']  			  	= 'Extend News';
$lang['news_readmore_button_help']  			  	= 'To use the extended Newstext, click here:';
$lang['news_message'] 				  	= '新闻文本';
$lang['news_permissions']			  	= '许可';

$lang['news_permissions_text']			= '不要显示新闻 for';
$lang['news_permissions_guest']			= '来宾';
$lang['news_permissions_member']		= '来宾和成员 (只有管理员可见)';
$lang['news_permissions_all']			= 'Free for all';
$lang['news_readmore'] 				  	= 'Read more...';

$lang['recruitment_open']				= 'Recruitment open';
$lang['recruitment_contact']			= 'contact';

$lang['sig_conf']						= '点击图片获得 BB Code';
$lang['sig_show']						= '为你的论坛显示 WoW 标签';

//Shirtshop
$lang['service']					    = '服务';
$lang['shirt_ad1']					    = 'Go to the Shirt-shop. <br> get your own shirt now!';
$lang['shirt_ad2']					    = 'Choose your Char';
$lang['shirt_ad3']					    = 'welcome to your guild shop ';
$lang['shirt_ad4']					    = 'W鋒le eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bed黵fnissen anpassen und jeden Schriftzug ver鋘dern.<br>
										   Unter Motive findest alle zur Verf黦ung stehenden Motive!';

$lang['error_iframe']					= "你的浏览器不支持框架!";
$lang['new_window']						= '在新窗口打开商店';
$lang['your_name']						= '你的名字';
$lang['your_guild']						= '你的公会';
$lang['your_server']					= '你的服务器';

//Last Raids
$lang['last_raids']					    = 'Last Raids';

$lang['voice_error']				    = 'No connection to the server.';

$lang['login_bridge_notice']		    = 'Login - CMS-Bridge is active. Use your CSM/Board Data to login.';
$lang['ads_remove']		    			= 'deactivate Advertising';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'Das Eqdkp-Plus ist ein Hobby-Projekt welches hauptsächlich von 2 privat Personen entwickelt und voran getrieben wird.
										  Am Anfang funktionierte das auch gut, doch nach knapp 3 Jahren wachsen uns die Kosten für das Projekt über
										  den Kopf. Alleine der Developer und Update Server kostet uns 600 Euro im Jahr. Dazu kommen nochmal 1000 Euro Kosten für
										  einen Anwalt, da es z.Z. einige rechtliche Probleme gibt.
										  In Zukunft soll es weitere Server-Basierende Features geben, die evtl. noch einen weiteren Server nötig machen.
										  Dazu kommen noch weitere Kosten wie die Lizenz für das neue Forum und der Designer unserer neuen Homepage.
										  Diese Kosten zusammen mit der Arbeitszeit können wir einfach nicht mehr aus eigener Tasche aufbringen. 
										  
										  Um das Projekt aber nicht sterben zu lassen, gibt es nun vereinzelt Werbebanner im Eqdkp-Plus. Diese Werbebanner unterliegen 
										  einigen Einschränkungen. So wird es kein pornografischen Werbung geben, ebenso wie es keine Gold/Item Verkäufer Werbung geben wird.
										  <br><br>
										  Ihr habt allerdings die Möglichkeit diese Werbung abzuschalten. Dazu habt ihr mehrere Möglichkeiten:<br><br>
										  <ol>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet einen Beitrag den ihr selber bestimmen könnt. 
										  	  Denkt darüber nach, was euch das Eqdkp-Plus wert ist. Nach einer Spende (egal ob Amazon oder Paypal) bekommt ihr eine Email mit dem Freischaltcode
										  	  <br>Die Freischaltung gilt dann für die jeweilige Version bzw. Major-Version.<br><br></li>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet mehr als 50 Euro. 
										  	   Ihr werdet damit Premium User und bekommt einen Livetime-Premium-Account mit dem ihr zu Updates auf neue 
										  	   Major Versionen berechtigt seid.</li><br>
										  <li> Geht auf <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> und spendet mehr als 100 Euro. 
										  	   Ihr werdet damit Gold-User und bekommt einen Livetime-Premium-Account mit dem ihr zu Updates auf neue 
										  	   Major Versionen bereichtigt seid und zusätzlich persönlichen Support von den Entwicklern.<br><br></li>
										  </ul>
										  <li>Alle Developer und Übersetzter die einen Beitrag zum Eqdkp-Plus geleistet haben, bekommen ebenfalls einen Freischaltcode.<br><br></li>
										  <li>Besonders angegiert Betatester bekommen ebenfalls einen Freischaltcode.<br><br></li>
										  </ol>
										  Das Geld was wir mit der Werbung bzw. den Spenden einnehmen verwenden wir ausschließlich um die Kosten des Projektes zu decken.<br>
										  
										  Das Eqdkp-Plus ist und bleibt ein non-profit Projekt!
										  ';



$lang['talents'] = array(
'Paladin'   => array('神圣','防护','恢复'),
'Rogue'     => array('刺杀','战斗','敏锐'),
'Warrior'   => array('武器','狂暴','防御'),
'Hunter'    => array('野兽掌握','射击','生存'),
'Priest'    => array('戒律','神圣','暗影'),
'Warlock'   => array('痛苦','恶魔','毁灭'),
'Druid'     => array('平衡','野性战斗','恢复'),
'Mage'      => array('奥术','火焰','冰霜'),
'Shaman'    => array('元素','增强','恢复'),
'Death Knight'   => array('Blood','Frost','Unholy')
);
$lang['portalmanager'] = 'Manage Portal Modules';

$lang['air_img_resize_warning'] = 'Click this bar to view the full image. The original is %1$sx%2$s.';

$lang['guild_shop'] = 'Shop';

// LibLoader Language String
$lang['libloader_notfound'] = 'The Library Loader Class is not available. Please check if the folder  "eqdkp/libraries/" is propperly uploaded!<br/> Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']   = "The Library '%1\$s' is outdated. You have to upload Version %2\$s or higher.<br/> Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";

$lang['more_plugins']   = "For more Plugins visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "For more Modules visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "For more Style visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Cancel';

// Update Available
$lang['upd_available_head']    = 'System Updated available'; 
$lang['upd_available_txt']     = 'The System is not up to date. There are updates available.';
$lang['upd_available_link']    = 'Click to show updates.';

$lang['menu_roster'] = 'Roster';

$lang['lib_cache_notwriteable'] = 'The folder "eqdkp/data" is not writable. Please chmod 777!';

//Sticky news
$lang['sticky_news_prefix'] = 'Sticky:';
$lang['news_sticky'] = 'Make it sticky?';

//pdh listmember
$lang['manage_members'] = "Manage members";
$lang['show_hidden_ranks'] = "Show hidden ranks";
$lang['show_inactive'] = "Show inactive";

// Libraries
$lang = array_merge($lang, array(
  
  // JS Short Language
  'cl_shortlangtag'           => 'en',
    
  // Update Check
  'cl_update_box'             => 'New Version available',
  'cl_changelog_url'          => 'Changelog',
  'cl_timeformat'             => 'm/d/Y',
  'cl_noserver'               => 'An error occurred while trying to contact the update server, either your host does not allow outbound connections
                                  or the error was caused by a network problem.
                                  Please visit the eqdkp-plugin-forum to make sure you are running the latest plugin version.',
  'cl_update_available'       => "Please update the installed <i>%1\$s</i> Plugin.
                                  Your current version is <b>%2\$s</b> and the latest version is <b>%3\$s (Released at: %4\$s)</b>.<br/><br/>
                                  [release: %5\$s]%6\$s%7\$s",
  'cl_update_url'             => 'To the Download Page',

  // Plugin Updater
  'cl_update_box'             => 'Database update required',
  'cl_upd_wversion'           => "The actual Database ( Version %1\$s ) does not fit to the installed Plugin Version %2\$s.
                                  Please use the update button to perform the required updates automatically.",
  'cl_upd_woversion'          => 'A previous installation was found. The version Data is missing. 
                                  Please choose the previous installed version in the drop Down list, to perform all Database changes.',
  'cl_upd_bttn'               => 'Update Database',
  'cl_upd_no_file'            => 'Update file is missing',
  'cl_upd_glob_error'         => 'An error occured during the update process.',
  'cl_upd_ok'                 => 'The update of the Database was successful',
  'cl_upd_step'               => 'Step',
  'cl_upd_step_ok'            => 'Successfull',
  'cl_upd_step_false'         => 'Failed',
  'cl_upd_reload_txt'         => 'Settings are reloading, please wait...',
  'cl_upd_pls_choose'         => 'Please choose...',
  'cl_upd_prev_version'       => 'Previous Version',

  // HTML Class
  'cl_on'                     => 'On',
  'cl_off'                    => 'Off',
  
    // ReCaptcha Library
	'lib_captcha_head'					=> 'confirmation Code',
	'lib_captcha_insertword'		=> 'Enter the words written below',
	'lib_captcha_insertnumbers' => 'Enter the spoken Numbers',
	'lib_captcha_send'					=> 'Send confirmation Code',
));

#$lang['']    								  = '';
?>
