<?php

namespace MediaWiki\Extension\RandomRules;

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\Subquery;

class RandomRulesHookHandler implements \MediaWiki\Hook\RandomPageQueryHook, \MediaWiki\Api\Hook\ApiQueryBaseBeforeQueryHook
{
	public function onRandomPageQuery(&$tables, &$conds, &$joinConds)
	{
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$templates = $config->get('RandomRulesTemplates');
		$categories = $config->get('RandomRulesCategories');

		if (count($categories['list']) > 0) {
			if ($categories["include"]) {
				$tables[] = 'categorylinks';
				$joinConds["categorylinks"] = ['INNER JOIN', 'cl_from=page_id'];
				$conds['cl_to'] = str_replace(" ", "_", $categories["list"]);
			} else {
				$tables['exclude'] = new Subquery("SELECT * FROM `categorylinks` WHERE cl_to IN ('" . implode("', '", str_replace(" ", "_", $categories["list"])) . "')");
				$joinConds['exclude'] = ['LEFT JOIN', 'cl_from=page_id'];
				$conds['cl_from'] = null;
			}
		}

		if (count($templates["list"]) > 0) {
			if ($templates["include"]) {
				$tables['nested'] = ['templatelinks', 'lt' => 'linktarget'];
				$joinConds['lt'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$joinConds['nested'] = ['INNER JOIN', 'tl_from=page_id'];
				$conds['lt_title'] = str_replace(" ", "_", $templates["list"]);
			} else {
				$tables['nested'] = ['templatelinks', 'exclude' => new Subquery("SELECT * FROM `linktarget` WHERE lt_title IN ('" . implode("', '", str_replace(" ", "_", $templates["list"])) . "')")];
				$joinConds['exclude'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$joinConds['nested'] = ['LEFT JOIN', 'tl_from=page_id'];
				$conds['tl_from'] = null;
			}
		}

		return true;
	}

	public function onApiQueryBaseBeforeQuery($module, &$tables, &$fields, &$conds, &$query_options, &$join_conds, &$hookData)
	{
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$templates = $config->get('RandomRulesTemplates');
		$categories = $config->get('RandomRulesCategories');

		if (count($categories['list']) > 0) {
			if ($categories["include"]) {
				$tables[] = 'categorylinks';
				$join_conds["categorylinks"] = ['INNER JOIN', 'cl_from=page_id'];
				$conds['cl_to'] = str_replace(" ", "_", $categories["list"]);
			} else {
				$tables['exclude'] = new Subquery("SELECT * FROM `categorylinks` WHERE cl_to IN ('" . implode("', '", str_replace(" ", "_", $categories["list"])) . "')");
				$join_conds['exclude'] = ['LEFT JOIN', 'cl_from=page_id'];
				$conds['cl_from'] = null;
			}
		}

		/* Unfortunately, for this second part to work,
		*  line 97 in ApiQueryRandom.php must be changed from
		*  $res = $this->select(__METHOD__);
		*  to
		*  $res = $this->select(__METHOD__, null, $path);
		*
		*  This is a kludge, but one I have not yet found a way around.
		*  Sorry.                                                        */

		if (count($templates["list"]) > 0) {
			if ($templates["include"]) {
				$tables['nested'] = ['templatelinks', 'lt' => 'linktarget'];
				$join_conds['lt'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$join_conds['nested'] = ['INNER JOIN', 'tl_from=page_id'];
				$conds['lt_title'] = str_replace(" ", "_", $templates["list"]);
			} else {
				$tables['nested'] = ['templatelinks', 'exclude' => new Subquery("SELECT * FROM `linktarget` WHERE lt_title IN ('" . implode("', '", str_replace(" ", "_", $templates["list"])) . "')")];
				$join_conds['exclude'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$join_conds['nested'] = ['LEFT JOIN', 'tl_from=page_id'];
				$conds['tl_from'] = null;
			}
		}

		return true;
	}
}
