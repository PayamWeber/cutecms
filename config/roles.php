<?php
return [
    'roles' => [
        [
            'title' => 'Administrator',
            'name' => 'administrator',
            'capabilities' => ['all'],
            'is_admin' => true,
            'is_default' => false,
        ],
        [
            'title' => 'Blog Editor',
            'name' => 'blog',
            'capabilities' => '',
            'is_admin' => true,
            'is_default' => true,
        ],
    ],
    'categories' => [
        [
            'title' => 'Dashboard',
            'name' => 'dashboard',
            'caps' => [
                [
                    'title' => 'See Dashboard',
                    'name' => 'dashboard_index',
                    'route' => 'dashboard',
                ],
            ],
        ],
        [
            'title' => 'Tasks',
            'name' => 'tasks',
            'caps' => [
                [
                    'title' => 'See Tasks',
                    'name' => 'task_index',
                    'route' => 'admin.task.index',
                ],
                [
                    'title' => 'See Others Tasks',
                    'name' => 'task_index_other',
                    'route' => 'admin.task.index',
                ],
                [
                    'title' => 'Create Tasks',
                    'name' => 'task_create',
                    'route' => [ 'admin.task.store' ],
                ],
                [
                    'title' => 'Edit Tasks',
                    'name' => 'task_edit',
                    'route' => [ 'admin.task.edit', 'admin.task.update' ],
                ],
                [
                    'title' => 'Edit Others Tasks',
                    'name' => 'task_edit_other',
                    'route' => [ 'admin.task.edit', 'admin.task.update' ],
                ],
                [
                    'title' => 'Delete Tasks',
                    'name' => 'task_delete',
                    'route' => 'admin.task.destroy',
                ],
                [
                    'title' => 'Delete Others Tasks',
                    'name' => 'task_delete_other',
                    'route' => 'admin.task.destroy',
                ],
            ],
        ],
		[
			'title' => 'Pages',
			'name' => 'page',
			'caps' => [
				[
					'title' => 'See Pages',
					'name' => 'page_index',
					'route' => 'admin.page.index',
				],
				[
					'title' => 'Create Pages',
					'name' => 'page_create',
					'route' => [ 'admin.page.create', 'admin.page.store' ],
				],
				[
					'title' => 'Edit Pages',
					'name' => 'page_edit',
					'route' => [ 'admin.page.edit', 'admin.page.update' ],
				],
				[
					'title' => 'Delete Pages',
					'name' => 'page_delete',
					'route' => 'admin.page.destroy',
				],
			],
		],
		[
			'title' => 'Posts',
			'name' => 'post',
			'caps' => [
				[
					'title' => 'See Posts',
					'name' => 'post_index',
					'route' => 'admin.post.index',
				],
				[
					'title' => 'See Other Posts',
					'name' => 'post_index_other',
					'route' => 'admin.post.index',
				],
				[
					'title' => 'Create Posts',
					'name' => 'post_create',
					'route' => [ 'admin.post.create', 'admin.post.store' ],
				],
				[
					'title' => 'Edit Posts',
					'name' => 'post_edit',
					'route' => [ 'admin.post.edit', 'admin.post.update' ],
				],
				[
					'title' => 'Edit Other Posts',
					'name' => 'post_edit_other',
					'route' => [ 'admin.post.edit', 'admin.post.update' ],
				],
				[
					'title' => 'Delete Posts',
					'name' => 'post_delete',
					'route' => 'admin.post.destroy',
				],
				[
					'title' => 'Delete Other Posts',
					'name' => 'post_delete_other',
					'route' => 'admin.post.destroy',
				],
			],
		],
        [
            'title' => 'Post Categories',
            'name' => 'post_category',
            'caps' => [
                [
                    'title' => 'See Categories',
                    'name' => 'post_category_index',
                    'route' => 'admin.post_category.index',
                ],
                [
                    'title' => 'Create Categories',
                    'name' => 'post_category_create',
                    'route' => 'admin.post_category.store',
                ],
                [
                    'title' => 'Edit Categories',
                    'name' => 'post_category_edit',
                    'route' => [ 'admin.post_category.edit', 'admin.post_category.update' ],
                ],
                [
                    'title' => 'Delete Categories',
                    'name' => 'post_category_delete',
                    'route' => 'admin.post.destroy',
                ],
            ],
        ],
        [
            'title' => 'Themes',
            'name' => 'themes',
            'caps' => [
                [
                    'title' => 'See Themes',
                    'name' => 'themes_index',
                    'route' => 'admin.appearance.theme.index',
                ],
                [
                    'title' => 'Activate Themes',
                    'name' => 'themes_activate',
                    'route' => 'admin.appearance.theme.set_active',
                ],
                [
                    'title' => 'Publish Themes',
                    'name' => 'themes_publish',
                    'route' => 'admin.appearance.theme.publish',
                ],
            ],
        ],
        [
            'title' => 'Media',
            'name' => 'media',
            'caps' => [
                [
                    'title' => 'See Self Files',
                    'name' => 'media_index',
                    'route' => 'panel.media.index',
                ],
                [
                    'title' => 'See Other\'s Files',
                    'name' => 'media_other_index',
                    'route' => 'panel.media.index',
                ],
                [
                    'title' => 'Upload Images',
                    'name' => 'media_store_image',
                    'route' => 'panel.media.store',
                ],
                [
                    'title' => 'Upload Other Type Files',
                    'name' => 'media_store_file',
                    'route' => 'panel.media.store',
                ],
                [
                    'title' => 'Update Self Files',
                    'name' => 'media_update_file',
                    'route' => 'panel.media.update',
                ],
                [
                    'title' => 'Update Other\'s Files',
                    'name' => 'media_update_other_file',
                    'route' => 'panel.media.update',
                ],
                [
                    'title' => 'Delete Self Files',
                    'name' => 'media_destroy_file',
                    'route' => 'panel.media.destroy',
                ],
                [
                    'title' => 'Delete Other\'s Files',
                    'name' => 'media_destroy_other_file',
                    'route' => 'panel.media.destroy',
                ],
            ],
        ],
        [
            'title' => 'Languages',
            'name' => 'languages',
            'caps' => [
                [
                    'title' => 'Edit translations',
                    'name' => 'edit_translations',
                    'route' => 'admin.string_translation.index',
                ],
            ],
        ],
        [
            'title' => 'Capability Categories',
            'name' => 'capability_cat',
            'caps' => [
                [
                    'title' => 'See Capability Categories',
                    'name' => 'capability_cat_index',
                    'route' => 'programming.capability_cats.index',
                ],
                [
                    'title' => 'Create Capability Category',
                    'name' => 'capability_cat_create',
                    'route' => 'programming.capability_cats.store',
                ],
                [
                    'title' => 'Edit Capability Categories',
                    'name' => 'capability_cat_edit',
                    'route' => [ 'programming.capability_cats.edit', 'programming.capability_cats.update' ],
                ],
            ],
        ],
		[
			'title' => 'Capabilities',
			'name' => 'capability',
			'caps' => [
				[
					'title' => 'See Capabilities',
					'name' => 'capabilities_index',
					'route' => 'programming.capabilities.index',
				],
				[
					'title' => 'Create Capability',
					'name' => 'capabilities_create',
					'route' => 'programming.capabilities.store',
				],
				[
					'title' => 'Edit Capabilities',
					'name' => 'capabilities_edit',
					'route' => [ 'programming.capabilities.edit', 'programming.capabilities.update' ],
				],
			],
		],
        [
            'title' => 'Roles',
            'name' => 'role',
            'caps' => [
                [
                    'title' => 'See Roles',
                    'name' => 'role_index',
                    'route' => 'admin.role.index',
                ],
                [
                    'title' => 'Create Role',
                    'name' => 'role_create',
                    'route' => 'admin.role.store',
                ],
                [
                    'title' => 'Edit Roles',
                    'name' => 'role_edit',
                    'route' => [ 'admin.role.edit', 'admin.role.update' ],
                ],
                [
                    'title' => 'Delete Role',
                    'name' => 'role_delete',
                    'route' => 'admin.role.destroy',
                ],
            ],
        ],
        [
            'title' => 'Users',
            'name' => 'user',
            'caps' => [
                [
                    'title' => 'See Users',
                    'name' => 'user_index',
                    'route' => 'admin.user.index',
                ],
                [
                    'title' => 'Create User',
                    'name' => 'user_create',
                    'route' => [ 'admin.user.create', 'admin.user.store' ],
                ],
                [
                    'title' => 'Edit User',
                    'name' => 'user_edit',
                    'route' => [ 'admin.user.edit', 'admin.user.update' ],
                ],
                [
                    'title' => 'Delete User',
                    'name' => 'user_delete',
                    'route' => 'admin.user.destroy',
                ],
            ],
        ],
    ],
];
