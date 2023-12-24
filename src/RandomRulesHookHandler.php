<?php

namespace MediaWiki\Extension\RandomRules;

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\Subquery;

class RandomRulesHookHandler implements \MediaWiki\Hook\RandomPageQueryHook, \MediaWiki\Api\Hook\ApiQueryBaseBeforeQueryHook
{
	public function onRandomPageQuery(&$tables, &$conds, &$joinConds)
	{
		// $config = \ConfigFactory::getDefaultInstance()->makeConfig('main');
		// $wgExcludeRandomPages = $config->get('ExcludeRandomPages');
		// if (!is_array($wgExcludeRandomPages) || empty($wgExcludeRandomPages)) {
		// 	return true;
		// }

		// $db = wfGetDB(DB_REPLICA);
		// foreach ($wgExcludeRandomPages as $cond) {
		// 	$pattern = $db->strencode($cond);
		// 	$pattern = str_replace(
		// 		['_', '%', ' ', '*'],
		// 		['\_', '\%', '\_', '%'],
		// 		$pattern
		// 	);
		// 	$extra[] = "`page_title` NOT LIKE '$pattern'";
		// }
		// bleg;
		// echo ("RANDSTR: ");
		// var_dump($randstr);
		// echo ("ISREDIR: ");
		// var_dump($isRedir);
		// echo ("NAMESPACES: ");
		// var_dump($namespaces);
		// echo ("EXTRA: ");
		// var_dump($extra);
		// echo ("TITLE: ");
		// var_dump($title);

		$config = MediaWikiServices::getInstance()->getMainConfig();
		$templates = $config->get('RandomRulesTemplates');
		$categories = $config->get('RandomRulesCategories');

		// var_dump($templates);
		// var_dump($categories);
		// var_dump($templates["list"]);

		if (count($categories['list']) > 0) {
			if ($categories["include"]) {
				$tables[] = 'categorylinks';
				$joinConds["categorylinks"] = ['INNER JOIN', 'cl_from=page_id'];
				$conds['cl_to'] = str_replace(" ", "_", $categories["list"]);
				// $tables[] = 'snorg';
			} else {
				$tables['exclude'] = new Subquery("SELECT * FROM `categorylinks` WHERE cl_to IN ('" . implode("', '", str_replace(" ", "_", $categories["list"])) . "')");
				$joinConds['exclude'] = ['LEFT JOIN', 'cl_from=page_id'];
				$conds['cl_from'] = null;
				// $tables[] = 'snorg';
			}
		}
		// var_dump($conds);
		// var_dump($joinConds);
		// bleg;
		// foreach ($templates["list"] as $template) {
		// 	if ($templates["include"])
		// 	{
		// 		$joinConds[] = ["categorylinks" => ['INNER JOIN', 'page_id=cl_from']];
		// 		$conds[] = []
		// 	};
		// 	else;
		// };
		if (count($templates["list"]) > 0) {
			if ($templates["include"]) {
				// $tables[] = 'templatelinks';
				// $tables[] = 'linktarget';
				$tables['nested'] = ['templatelinks', 'lt' => 'linktarget'];
				$joinConds['lt'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$joinConds['nested'] = ['INNER JOIN', 'tl_from=page_id'];
				$conds['lt_title'] = str_replace(" ", "_", $templates["list"]);
				// $tables[] = 'snorg';
			} else {
				// $tables['exclude'] = new Subquery("SELECT * FROM `linktarget` WHERE lt_title IN ('" . implode("', '", str_replace(" ", "_", $templates["list"])) . "')");
				$tables['nested'] = ['templatelinks', 'exclude' => new Subquery("SELECT * FROM `linktarget` WHERE lt_title IN ('" . implode("', '", str_replace(" ", "_", $templates["list"])) . "')")];
				$joinConds['exclude'] = ['INNER JOIN', 'tl_target_id=lt_id'];
				$joinConds['nested'] = ['LEFT JOIN', 'tl_from=page_id'];
				$conds['tl_from'] = null;
				// $tables[] = 'snorg';
			}
		}
		// foreach ($templates["list"] as $template) {
		// 	if ($templates["include"]);
		// 	else;
		// };
		// var_dump($conds);
		// var_dump($joinConds);
		// bleg;

		return true;
	}

	public function onApiQueryBaseBeforeQuery($module, &$tables, &$fields, &$conds, &$query_options, &$join_conds, &$hookData)
	{
		return true;
	}
}
