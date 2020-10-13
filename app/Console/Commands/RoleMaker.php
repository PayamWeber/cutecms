<?php

namespace App\Console\Commands;

use App\Models\Capability;
use App\Models\CapabilityCat;
use App\Models\Role;
use Illuminate\Console\Command;

class RoleMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Roles and Capabilities inside of Config.php file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config( 'roles' );
        if ( $config )
        {
            if ( isset( $config[ 'roles' ] ) )
            {
                $roles = $config[ 'roles' ];

                if ( $roles )
                {
                    foreach ( $roles as $role )
                    {
                        $role[ 'title' ] = $role[ 'title' ] ?? '';
                        $role[ 'name' ]  = $role[ 'name' ] ?? '';
                        $role[ 'capabilities' ]  = $role[ 'capabilities' ] ?? '';
                        $role[ 'is_admin' ]  = isset( $role[ 'is_admin' ] ) ? (int)$role[ 'is_admin' ] : 0;
                        $role[ 'is_default' ]  = isset( $role[ 'is_default' ] ) ? (int)$role[ 'is_default' ] : 0;

                        if ( ! $role[ 'name' ] )
                            continue;
                        echo "Making Role \e[0;32m'" . $role['name'] . "'\e[0m\n";

                        $model = Role::where( 'name', $role[ 'name' ] )->first();

                        if ( $model )
                        {
                            $model->title = $role[ 'title' ];
                            $model->capabilities = $role[ 'capabilities' ];
                            $model->is_admin = (string)$role[ 'is_admin' ];
                            $model->is_default = (string)$role[ 'is_default' ];
                            $model->save();
                        } else
                        {
                            $model        = new Role();
                            $model->title = $role[ 'title' ];
                            $model->name  = $role[ 'name' ];
                            $model->capabilities = $role[ 'capabilities' ];
                            $model->is_admin = (string)$role[ 'is_admin' ];
                            $model->is_default = (string)$role[ 'is_default' ];
                            $model->save();
                        }
                    }
                }
            }
            if ( isset( $config[ 'categories' ] ) )
            {
                $cats = $config[ 'categories' ];

                if ( $cats )
                {
                    $sorter = 1;
                    foreach ( $cats as $cat )
                    {
                        $cat[ 'title' ] = $cat[ 'title' ] ?? '';
                        $cat[ 'name' ]  = $cat[ 'name' ] ?? '';
                        $cat[ 'caps' ]  = $cat[ 'caps' ] ?? '';

                        if ( ! $cat[ 'name' ] )
                            continue;
                        echo "\nMaking Category '" . $cat['name'] . "'\n";

                        $model = CapabilityCat::where( 'name', $cat[ 'name' ] )->first();

                        if ( $model )
                        {
                            $model->title = $cat[ 'title' ];
                            $model->order = $sorter . '0';
                            $model->save();
                        } else
                        {
                            $model        = new CapabilityCat();
                            $model->title = $cat[ 'title' ];
                            $model->name  = $cat[ 'name' ];
                            $model->order = $sorter . '0';
                            $model->save();
                        }
                        $parent = $model;

                        if ( $cat[ 'caps' ] && is_array( $cat[ 'caps' ] ) )
                        {
                            foreach ( $cat[ 'caps' ] as $cap )
                            {
                                $cap[ 'title' ] = $cap[ 'title' ] ?? '';
                                $cap[ 'name' ]  = $cap[ 'name' ] ?? '';
                                $cap[ 'route' ] = $cap[ 'route' ] ?? '';

                                if ( ! $cap[ 'name' ] )
                                    continue;
                                echo "Making Cap ----------\e[0;33m'" . $cap['name'] . "'\e[0m\n";

                                $model = Capability::where( 'name', $cap[ 'name' ] )->first();

                                if ( $model )
                                {
                                    $model->title = $cap[ 'title' ];
                                    $model->route = is_array( $cap[ 'route' ] ) ? json_encode( $cap[ 'route' ] ) : ( $cap[ 'route' ] ? $cap[ 'route' ] : '' );
                                    $model->parent = $parent->id;
                                    $model->save();
                                } else
                                {
                                    $model        = new Capability();
                                    $model->title = $cap[ 'title' ];
                                    $model->name  = $cap[ 'name' ];
                                    $model->route = is_array( $cap[ 'route' ] ) ? json_encode( $cap[ 'route' ] ) : ( $cap[ 'route' ] ? $cap[ 'route' ] : '' );
                                    $model->parent = $parent->id;
                                    $model->save();
                                }
                            }
                        }
                        $sorter++;
                    }
                }
            }
            echo "\n\e[0;35mAll Roles and Caps inserted.\e[0m\n";
        } else
        {
            echo "\n\e[0;31mConfig file does not exist !\e[0m\n";
        }
    }
}
