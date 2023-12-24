<?php

namespace MediaWiki\Extension\RandomRules;

use MediaWiki\MediaWikiServices;

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

		if ($categories["include"]) {
			$tables[] = 'categorylinks';
			$joinConds["categorylinks"] = ['INNER JOIN', 'cl_from=page_id'];
			$conds['cl_to'] = str_replace(" ", "_", $categories["list"]);
		}

		var_dump($conds);
		var_dump($joinConds);
		// bleg;
		// foreach ($templates["list"] as $template) {
		// 	if ($templates["include"])
		// 	{
		// 		$joinConds[] = ["categorylinks" => ['INNER JOIN', 'page_id=cl_from']];
		// 		$conds[] = []
		// 	};
		// 	else;
		// };
		foreach ($templates["list"] as $category) {
			if ($templates["include"]);
			else;
		};
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
