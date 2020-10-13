<?php
return [
	"dashboard" => [
		"title" => 'Dashboard',
		"url" => 'dashboard',
		"icon" => "la-home",
		"capability" => "dashboard_index",
	],
	"pages" => [
		"title" => 'Pages',
		"icon" => "la-users",
		"capability" => [],
		"sub" => [
			[
				'title' => 'All',
				"capability" => 'page_index',
				"url" => 'admin.page.index',
			],
			[
				'title' => 'Add',
				"capability" => 'page_create',
				"url" => 'admin.page.create',
			],
		],
	],
	"posts" => [
		"title" => 'Posts',
		"icon" => "la-pagelines",
		"capability" => [],
		"sub" => [
			[
				'title' => 'All',
				"capability" => 'post_index',
				"url" => 'admin.post.index',
			],
			[
				'title' => 'Add',
				"capability" => 'post_create',
				"url" => 'admin.post.create',
			],
			[
				'title' => 'Categories',
				"capability" => 'post_category_index',
				"url" => 'admin.post_category.index',
			],
		],
	],
	"media" => [
		"title" => 'Media',
		"icon" => "la-files-o",
		"capability" => [],
		'url' => 'admin.media.index',
	],
	"themes" => [
		"title" => 'Themes',
		"icon" => "la-paint-brush",
		"capability" => 'themes_index',
		"url" => 'admin.appearance.theme.index'
	],
	"lang" => [
		"title" => 'Language',
		"icon" => "la-language",
		"capability" => [],
		"sub" => [
			"users" => [
				'title' => 'String Translation',
				'url' => 'admin.string_translation.index',
				"capability" => '',
			],
		],
	],
	"users" => [
		"title" => 'Users',
		"icon" => "la-users",
		"capability" => [],
		"sub" => [
			[
				'title' => 'All',
				"capability" => 'user_index',
				"url" => 'admin.user.index',
			],
			[
				'title' => 'Add',
				"capability" => 'user_create',
				"url" => 'admin.user.create',
			],
		],
	],
	"roles" => [
		'title' => 'Roles',
		"capability" => 'role_index',
		"url" => 'admin.role.index',
		"icon" => "la-hand-o-up",
	],
	/*"programmer" => [
		"title" => 'Programmer',
		"icon" => "la-code",
		"capability" => [],
		"capability_operator" => "or",
		"sub" => [
			"capabilities" => [
				'title' => 'Capabilities',
				'icon' => '',
				"capability" => [],
				"sub" => [
					"capabilities" => [
						'title' => 'List',
						"url" => 'programming.capabilities.index',
						"capability" => 'capabilities_index',
						"capability_operator" => "and",
					],
					"capability_cats" => [
						'title' => 'Categories',
						"url" => 'programming.capability_cats.index',
						"capability" => 'capability_cat_index',
						"capability_operator" => "and",
					],
				],
			],
		],
	],*/
];
