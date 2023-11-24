<?php
if (!defined('EQDKP_INC')) {
        die('Do not access this file directly.');
}

if (!class_exists('exchange_points2')) {
        class exchange_points2 extends gen_class {
                public static $shortcuts = array('pex'=>'plus_exchange');
                public $options = array();

                public function get_points2($params, $arrBody) {
                        $isAPITokenRequest = $this->pex->getIsApiTokenRequest();
                        $charName = $params['get']['character'];
                        if ($isAPITokenRequest || $this->user->check_pageobjects(array('points'), 'AND', false)) {
                                $userId = -1;

                                $pointsQuery = $this->db->prepare("select points from __members where member_id in (select member_id from __member_user where user_id = (select user_id from __member_user where member_id = (select member_id from __members where lower(member_name) = ?)));")->execute(strtolower($charName));
                                if ($pointsQuery) {
                                        $totalDkp = -1;

                                        while ($pointsRow = $pointsQuery->fetchAssoc()) {
                                                if ($totalDkp < 0) $totalDkp++;
                                                $pointsData = unserialize($pointsRow['points']);
                                                $totalDkp += ($pointsData[1][0] - $pointsData[1][1] + $pointsData[1][2]);
                                        }

                                        if ($totalDkp < 0) {
                                                exit("Unable to find any points for character with name \"{$charName}\"");
                                        } else {
                                                exit($totalDkp);
                                        }
                                } else {
                                        exit("Unable to find a user for character with name \"{$charName}\"");
                                }
                        } else {
                                return $this->pex->error('access denied');
                        }
                }
        }
}
